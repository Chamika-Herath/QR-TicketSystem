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
        Storage::disk('public')->put("qrcodes/{$token}.png", $qrBytes);
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
            $tribalTemplatePath = public_path('images/templates/tribal_ticket_template.jpg');

            if (file_exists($tribalTemplatePath)) {
                // Decode the Tribal/African theme background template (768x1376)
                $image = $manager->decode($tribalTemplatePath);

                $boldFont = file_exists(public_path('fonts/Inter-Bold.ttf')) ? public_path('fonts/Inter-Bold.ttf') : null;
                $regularFont = file_exists(public_path('fonts/Inter-Regular.ttf')) ? public_path('fonts/Inter-Regular.ttf') : null;

                // 1. Overlay QR code inside the ornate tribal frame (frame center ~ 384, 441)
                $qrImage = $manager->decode(Storage::path($qrPath));
                $qrImage->resize(215, 215);
                $image->insert($qrImage, 277, 333);

                // 2. Event Name
                $image->text(strtoupper($event->name), 384, 625, function ($font) use ($boldFont) {
                    if ($boldFont) $font->file($boldFont);
                    $font->size(34);
                    $font->color('#2D1606');
                    $font->align('center', 'center');
                });

                // 3. Attendee Name
                $image->text(strtoupper($attendee->name), 384, 710, function ($font) use ($boldFont) {
                    if ($boldFont) $font->file($boldFont);
                    $font->size(30);
                    $font->color('#1C1917');
                    $font->align('center', 'center');
                });

                // 4. Event Date & Time
                $dateStr = strtoupper($event->starts_at->format('d F Y')) . '  •  ' . $event->starts_at->format('h:i A');
                $image->text($dateStr, 384, 768, function ($font) use ($regularFont) {
                    if ($regularFont) $font->file($regularFont);
                    $font->size(22);
                    $font->color('#5C3A21');
                    $font->align('center', 'center');
                });

                // 5. Venue
                $image->text(strtoupper($event->venue), 384, 818, function ($font) use ($regularFont) {
                    if ($regularFont) $font->file($regularFont);
                    $font->size(20);
                    $font->color('#785032');
                    $font->align('center', 'center');
                });

                // 6. Ticket Type / Seat Badge
                $seatInfo = $ticket->seat_number ? ('  •  SEAT ' . strtoupper($ticket->seat_number)) : '';
                $typeStr = strtoupper($ticket->ticket_type) . $seatInfo;
                $image->text($typeStr, 384, 880, function ($font) use ($boldFont) {
                    if ($boldFont) $font->file($boldFont);
                    $font->size(24);
                    $font->color('#B43416');
                    $font->align('center', 'center');
                });

                // 7. Bottom Stub Section (below tear-off line)
                $image->text('TICKET ID : ' . strtoupper(substr($ticket->token, 0, 12)), 384, 1120, function ($font) use ($boldFont) {
                    if ($boldFont) $font->file($boldFont);
                    $font->size(22);
                    $font->color('#2D1606');
                    $font->align('center', 'center');
                });

                $image->text('End of an Era — Farewell 2026', 384, 1160, function ($font) use ($regularFont) {
                    if ($regularFont) $font->file($regularFont);
                    $font->size(18);
                    $font->color('#785032');
                    $font->align('center', 'center');
                });

                $image->text('Powered by DeepNix', 384, 1200, function ($font) use ($regularFont) {
                    if ($regularFont) $font->file($regularFont);
                    $font->size(15);
                    $font->color('#8B5A2B');
                    $font->align('center', 'center');
                });
            } else {
                // Fallback canvas if template file is missing
                $image = $manager->createImage(800, 1400);
                $image->fill('#FDB849');
                $image->drawRectangle(function ($draw) {
                    $draw->at(0, 0);
                    $draw->size(800, 750);
                    $draw->background('#13476E');
                });

                $qrImage = $manager->decode(Storage::path($qrPath));
                $qrImage->resize(380, 380);
                $image->insert($qrImage, 210, 130);
            }
        }

        // Ensure target directories exist
        Storage::makeDirectory('public/tickets');
        Storage::disk('public')->makeDirectory('tickets');

        // Save composite ticket image
        $ticketSavePath = "public/tickets/{$token}.png";
        $image->save(Storage::path($ticketSavePath));
        $image->save(Storage::disk('public')->path("tickets/{$token}.png"));
        $ticket->ticket_image_path = "storage/tickets/{$token}.png";
        $ticket->save();

        return [
            'qr_image_path' => $ticket->qr_image_path,
            'ticket_image_path' => $ticket->ticket_image_path,
        ];
    }
}
