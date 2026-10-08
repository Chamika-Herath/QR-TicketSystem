<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Ticket;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TicketGeneratorService
{
    /**
     * Generate unique token, QR code, composite onto ticket template, and save.
     */
    public function generate(Ticket $ticket): array
    {
        $attendee = $ticket->attendee;
        $event = $attendee->event;

        // 1. Generate unique token if not present
        if (!$ticket->token) {
            do {
                $token = Str::random(32);
            } while (Ticket::where('token', $token)->exists());
            $ticket->token = $token;
            $ticket->save();
        }

        $token = $ticket->token;

        // 2. Generate QR code using Endroid/QrCode (returns PNG content)
        $builder = new Builder(
            writer: new PngWriter(),
            data: $token,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 400, // Appropriate size for the new vertical layout
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );
        $qrResult = $builder->build();

        $qrBytes = $qrResult->getString();
        
        // Save QR code image
        $qrPath = "public/qrcodes/{$token}.png";
        Storage::put($qrPath, $qrBytes);
        $ticket->qr_image_path = "storage/qrcodes/{$token}.png";

        // 3. Composite onto Ticket Template using Intervention Image
        $manager = new ImageManager(new Driver());
        
        // Use uploaded template, or create a default rich card template if not provided
        if ($event->ticket_template_path && Storage::exists('public/' . str_replace('storage/', '', $event->ticket_template_path))) {
            $realTemplatePath = Storage::path('public/' . str_replace('storage/', '', $event->ticket_template_path));
            $image = $manager->decode($realTemplatePath);
            
            // Overlay QR code at x=580, y=125 (right aligned card area)
            $qrImage = $manager->decode(Storage::path($qrPath));
            $qrImage->resize(150, 150);
            $image->insert($qrImage, 580, 125);

            $fontFile = public_path('fonts/Inter-Regular.ttf');

            $image->text($attendee->name, 50, 150, function ($font) use ($fontFile) {
                $font->file($fontFile);
                $font->size(32);
                $font->color('#f8fafc');
            });

            $image->text($event->name, 50, 80, function ($font) use ($fontFile) {
                $font->file($fontFile);
                $font->size(24);
                $font->color('#38bdf8');
            });

            $image->text('Date: ' . $event->starts_at->format('M d, Y h:i A'), 50, 220, function ($font) use ($fontFile) {
                $font->file($fontFile);
                $font->size(16);
                $font->color('#94a3b8');
            });

            $image->text('Venue: ' . $event->venue, 50, 260, function ($font) use ($fontFile) {
                $font->file($fontFile);
                $font->size(16);
                $font->color('#94a3b8');
            });

            $image->text('Ticket Token: ' . substr($token, 0, 8) . '...', 50, 320, function ($font) use ($fontFile) {
                $font->file($fontFile);
                $font->size(12);
                $font->color('#64748b');
            });
        } else {
            // Create a gorgeous vertical canvas (800x1400)
            $image = $manager->createImage(800, 1400);
            
            // Fill bottom half with Orange/Yellow
            $image->fill('#FDB849'); 

            // Draw Top Blue Section
            $image->drawRectangle(function ($draw) {
                $draw->at(0, 0);
                $draw->size(800, 750);
                $draw->background('#13476E');
            });

            // Draw Blue Triangle pointing downwards
            $image->drawPolygon(function ($draw) {
                $draw->point(0, 750);
                $draw->point(400, 850);
                $draw->point(800, 750);
                $draw->background('#13476E');
            });

            // Draw cutout notches on the sides
            $image->drawCircle(function ($draw) {
                $draw->at(0, 750);
                $draw->radius(25);
                $draw->background('#FFFFFF');
            });
            $image->drawCircle(function ($draw) {
                $draw->at(800, 750);
                $draw->radius(25);
                $draw->background('#FFFFFF');
            });

            // Fonts (Use the bundled TTF font to ensure it works on Linux/Hostinger)
            $primaryFont = public_path('fonts/Inter-Regular.ttf');
            $secondaryFont = public_path('fonts/Inter-Regular.ttf');

            // --- TOP BLUE SECTION ---
            
            // Top Watermark
            $image->text('WWW.DEEPNIX.COM', 400, 70, function ($font) use ($primaryFont) {
                if ($primaryFont) $font->file($primaryFont);
                $font->size(22);
                $font->color('#FFFFFF');
                $font->align('center', 'center');
            });

            // White box for QR code
            $image->drawRectangle(function ($draw) {
                $draw->at(200, 120);
                $draw->size(400, 400);
                $draw->background('#FFFFFF');
                $draw->border('#FFFFFF', 2);
            });

            // Place QR Code
            $qrImage = $manager->decode(Storage::path($qrPath));
            $qrImage->resize(380, 380);
            $image->insert($qrImage, 210, 130);

            // "E-TICKET" Text
            $image->text('E-TICKET', 400, 620, function ($font) use ($primaryFont) {
                if ($primaryFont) $font->file($primaryFont);
                $font->size(85);
                $font->color('#FFFFFF');
                $font->align('center', 'center');
            });

            // Ticket Type & Date
            $ticketTypeStr = strtoupper($ticket->ticket_type) . ' - ' . strtoupper($event->starts_at->format('d.m.Y'));
            $image->text($ticketTypeStr, 400, 700, function ($font) use ($primaryFont) {
                if ($primaryFont) $font->file($primaryFont);
                $font->size(28);
                $font->color('#FFFFFF');
                $font->align('center', 'center');
            });

            // --- BOTTOM ORANGE SECTION ---

            // Event Name
            $image->text(strtoupper($event->name), 400, 930, function ($font) use ($primaryFont) {
                if ($primaryFont) $font->file($primaryFont);
                $font->size(46);
                $font->color('#2D3748');
                $font->align('center', 'center');
            });

            // Venue
            $image->text(strtoupper($event->venue), 400, 990, function ($font) use ($primaryFont) {
                if ($primaryFont) $font->file($primaryFont);
                $font->size(22);
                $font->color('#2D3748');
                $font->align('center', 'center');
            });

            // Event Sub-Date
            $eventDateStr = strtoupper($event->starts_at->format('d F Y'));
            $image->text($eventDateStr, 400, 1030, function ($font) use ($primaryFont) {
                if ($primaryFont) $font->file($primaryFont);
                $font->size(22);
                $font->color('#2D3748');
                $font->align('center', 'center');
            });

            // Details Grid
            $col1 = 150;
            $col2 = 450;
            $row1_label = 1130;
            $row1_val = 1170;
            $row2_label = 1230;
            $row2_val = 1270;

            // Ticket ID
            $image->text('Ticket ID :', $col1, $row1_label, function ($font) use ($primaryFont) {
                if ($primaryFont) $font->file($primaryFont);
                $font->size(26);
                $font->color('#2D3748');
            });
            $image->text(substr($ticket->token, 0, 12), $col1, $row1_val, function ($font) use ($secondaryFont) {
                if ($secondaryFont) $font->file($secondaryFont);
                $font->size(26);
                $font->color('#4A5568');
            });

            // Name
            $image->text('Name :', $col2, $row1_label, function ($font) use ($primaryFont) {
                if ($primaryFont) $font->file($primaryFont);
                $font->size(26);
                $font->color('#2D3748');
            });
            $image->text(strtoupper($attendee->name), $col2, $row1_val, function ($font) use ($secondaryFont) {
                if ($secondaryFont) $font->file($secondaryFont);
                $font->size(26);
                $font->color('#4A5568');
            });

            // Date
            $image->text('Date :', $col1, $row2_label, function ($font) use ($primaryFont) {
                if ($primaryFont) $font->file($primaryFont);
                $font->size(26);
                $font->color('#2D3748');
            });
            $image->text($event->starts_at->format('d.m.Y'), $col1, $row2_val, function ($font) use ($secondaryFont) {
                if ($secondaryFont) $font->file($secondaryFont);
                $font->size(26);
                $font->color('#4A5568');
            });

            // Time & Seat
            $image->text('Time / Seat :', $col2, $row2_label, function ($font) use ($primaryFont) {
                if ($primaryFont) $font->file($primaryFont);
                $font->size(26);
                $font->color('#2D3748');
            });
            $seatDisplay = $ticket->seat_number ? (' / ' . strtoupper($ticket->seat_number)) : '';
            $image->text($event->starts_at->format('H:i') . $seatDisplay, $col2, $row2_val, function ($font) use ($secondaryFont) {
                if ($secondaryFont) $font->file($secondaryFont);
                $font->size(26);
                $font->color('#4A5568');
            });

            // DeepNix Watermark at the very bottom
            $image->text('Powered by DeepNix Software Solutions', 400, 1360, function ($font) use ($primaryFont) {
                if ($primaryFont) $font->file($primaryFont);
                $font->size(16);
                $font->color('#B7791F'); // Darker orange/brown
                $font->align('center', 'center');
            });
        }

        // Ensure target directories exist
        Storage::makeDirectory('public/tickets');

        // Save composite ticket image
        $ticketSavePath = "public/tickets/{$token}.png";
        $image->save(Storage::path($ticketSavePath));
        $ticket->ticket_image_path = "storage/tickets/{$token}.png";
        $ticket->save();

        return [
            'qr_image_path' => $ticket->qr_image_path,
            'ticket_image_path' => $ticket->ticket_image_path,
        ];
    }
}
