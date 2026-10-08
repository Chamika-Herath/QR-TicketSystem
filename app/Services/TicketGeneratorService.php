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
            size: 800, // Tripled size for high quality
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

            $image->text($attendee->name, 50, 150, function ($font) {
                $font->size(32);
                $font->color('#f8fafc');
            });

            $image->text($event->name, 50, 80, function ($font) {
                $font->size(24);
                $font->color('#38bdf8');
            });

            $image->text('Date: ' . $event->starts_at->format('M d, Y h:i A'), 50, 220, function ($font) {
                $font->size(16);
                $font->color('#94a3b8');
            });

            $image->text('Venue: ' . $event->venue, 50, 260, function ($font) {
                $font->size(16);
                $font->color('#94a3b8');
            });

            $image->text('Ticket Token: ' . substr($token, 0, 8) . '...', 50, 320, function ($font) {
                $font->size(12);
                $font->color('#64748b');
            });
        } else {
            // Create a modern high-res canvas (2400x1200 - 3x scale)
            $image = $manager->createImage(2400, 1200);
            $image->fill('#F8FAFC'); // Light gray-blue background

            // Draw accent banner on the left (Orange Touch)
            $image->drawRectangle(function ($draw) {
                $draw->at(0, 0);
                $draw->size(60, 1200);
                $draw->background('#EA580C'); // Orange-600
            });

            // Draw white card area for ticket details
            $image->drawRectangle(function ($draw) {
                $draw->at(120, 60);
                $draw->size(1500, 1080);
                $draw->background('#FFFFFF');
                $draw->border('#CBD5E1', 3);
            });

            // Draw right stub area
            $image->drawRectangle(function ($draw) {
                $draw->at(1680, 60);
                $draw->size(660, 1080);
                $draw->background('#FFFFFF');
                $draw->border('#CBD5E1', 3);
            });

            // Draw dashed perforation line
            $image->drawLine(function ($draw) {
                $draw->from(1650, 60);
                $draw->to(1650, 1140);
                $draw->color('#94A3B8');
                $draw->width(6);
            });

            // Draw decorative dots (perforation cutouts)
            $image->drawCircle(function ($draw) {
                $draw->at(1650, 60);
                $draw->radius(24);
                $draw->background('#F8FAFC');
            });
            $image->drawCircle(function ($draw) {
                $draw->at(1650, 1140);
                $draw->radius(24);
                $draw->background('#F8FAFC');
            });

            // Draw decorative separator inside main card
            $image->drawLine(function ($draw) {
                $draw->from(210, 330);
                $draw->to(1530, 330);
                $draw->color('#E2E8F0');
                $draw->width(3);
            });

            // Set up font files with fallbacks
            $courier = '/System/Library/Fonts/Supplemental/Courier New.ttf';
            $arial = '/System/Library/Fonts/Supplemental/Arial.ttf';
            $hasCourier = file_exists($courier);
            $hasArial = file_exists($arial);

            // Write event details (Scaled by 3x)
            $image->text(strtoupper($event->name), 210, 225, function ($font) use ($arial, $hasArial) {
                if ($hasArial) {
                    $font->file($arial);
                    $font->size(66);
                }
                $font->color('#0F172A'); // Slate-900
            });

            $image->text(strtoupper($attendee->name), 210, 480, function ($font) use ($arial, $hasArial) {
                if ($hasArial) {
                    $font->file($arial);
                    $font->size(78);
                }
                $font->color('#EA580C'); // Orange-600
            });

            // VENUE
            $image->text('VENUE:', 210, 660, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(39);
                }
                $font->color('#64748B');
            });
            $image->text(strtoupper($event->venue), 540, 660, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(39);
                }
                $font->color('#0F172A');
            });

            // DATE
            $image->text('DATE/TIME:', 210, 780, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(39);
                }
                $font->color('#64748B');
            });
            $image->text(strtoupper($event->starts_at->format('M d, Y h:i A')), 540, 780, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(39);
                }
                $font->color('#0F172A');
            });

            // REF
            $image->text('TICKET REF:', 210, 900, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(39);
                }
                $font->color('#64748B');
            });
            $image->text(strtoupper(substr($ticket->token, 0, 16)), 540, 900, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(39);
                }
                $font->color('#EA580C'); // Orange-600
            });

            if ($ticket->seat_number) {
                $image->text('SEAT/ZONE:', 210, 1020, function ($font) use ($courier, $hasCourier) {
                    if ($hasCourier) {
                        $font->file($courier);
                        $font->size(39);
                    }
                    $font->color('#64748B');
                });
                $image->text(strtoupper($ticket->seat_number), 540, 1020, function ($font) use ($courier, $hasCourier) {
                    if ($hasCourier) {
                        $font->file($courier);
                        $font->size(39);
                    }
                    $font->color('#0F172A');
                });
            }

            // Right ticket stub details
            $image->text(strtoupper($ticket->ticket_type), 1770, 195, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(36);
                }
                $font->color('#64748B');
            });

            // Watermark / Brand on Stub
            $image->text('Powered by DeepNix software solutions', 1710, 1080, function ($font) use ($arial, $hasArial) {
                if ($hasArial) {
                    $font->file($arial);
                    $font->size(30);
                }
                $font->color('#94A3B8');
            });

            // White box background for QR code
            $image->drawRectangle(function ($draw) {
                $draw->at(1755, 345);
                $draw->size(510, 510);
                $draw->background('#FFFFFF');
                $draw->border('#E2E8F0', 3);
            });

            // Place High-Res QR Code
            $qrImage = $manager->decode(Storage::path($qrPath));
            $qrImage->resize(480, 480);
            $image->insert($qrImage, 1770, 360);
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
