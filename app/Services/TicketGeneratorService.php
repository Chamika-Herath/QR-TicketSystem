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
            size: 250,
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
            // Create a modern canvas from scratch (800x400)
            $image = $manager->createImage(800, 400);
            $image->fill('#0F172A'); // Slate-900 Dark theme

            // Draw accent banner on the left
            $image->drawRectangle(function ($draw) {
                $draw->at(0, 0);
                $draw->size(20, 400);
                $draw->background('#3B82F6'); // Blue-500
            });

            // Draw white card area for ticket details
            $image->drawRectangle(function ($draw) {
                $draw->at(40, 20);
                $draw->size(500, 360);
                $draw->background('#1E293B'); // Slate-800
                $draw->border('#334155', 1); // Slate-700
            });

            // Draw right stub area
            $image->drawRectangle(function ($draw) {
                $draw->at(560, 20);
                $draw->size(220, 360);
                $draw->background('#1E293B');
                $draw->border('#334155', 1);
            });

            // Draw dashed perforation line
            $image->drawLine(function ($draw) {
                $draw->from(550, 20);
                $draw->to(550, 380);
                $draw->color('#334155');
                $draw->width(2);
            });

            // Draw decorative dots (modern perforation feel)
            $image->drawCircle(function ($draw) {
                $draw->at(550, 20);
                $draw->radius(8);
                $draw->background('#0F172A');
            });
            $image->drawCircle(function ($draw) {
                $draw->at(550, 380);
                $draw->radius(8);
                $draw->background('#0F172A');
            });

            // Draw decorative separator inside main card
            $image->drawLine(function ($draw) {
                $draw->from(70, 110);
                $draw->to(510, 110);
                $draw->color('#334155');
                $draw->width(1);
            });

            // Set up font files with fallbacks
            $courier = '/System/Library/Fonts/Supplemental/Courier New.ttf';
            $arial = '/System/Library/Fonts/Supplemental/Arial.ttf';
            $hasCourier = file_exists($courier);
            $hasArial = file_exists($arial);

            // Write event details onto the modern ticket
            $image->text(strtoupper($event->name), 70, 75, function ($font) use ($arial, $hasArial) {
                if ($hasArial) {
                    $font->file($arial);
                    $font->size(22);
                }
                $font->color('#F8FAFC'); // Slate-50
            });

            $image->text(strtoupper($attendee->name), 70, 160, function ($font) use ($arial, $hasArial) {
                if ($hasArial) {
                    $font->file($arial);
                    $font->size(26);
                }
                $font->color('#38BDF8'); // Sky-400
            });

            // VENUE
            $image->text('VENUE:', 70, 220, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#94A3B8');
            });
            $image->text(strtoupper($event->venue), 180, 220, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#F8FAFC');
            });

            // DATE
            $image->text('DATE/TIME:', 70, 260, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#94A3B8');
            });
            $image->text(strtoupper($event->starts_at->format('M d, Y h:i A')), 180, 260, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#F8FAFC');
            });

            // REF
            $image->text('TICKET REF:', 70, 300, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#94A3B8');
            });
            $image->text(strtoupper(substr($ticket->token, 0, 16)), 180, 300, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#38BDF8');
            });

            if ($ticket->seat_number) {
                $image->text('SEAT/ZONE:', 70, 340, function ($font) use ($courier, $hasCourier) {
                    if ($hasCourier) {
                        $font->file($courier);
                        $font->size(13);
                    }
                    $font->color('#94A3B8');
                });
                $image->text(strtoupper($ticket->seat_number), 180, 340, function ($font) use ($courier, $hasCourier) {
                    if ($hasCourier) {
                        $font->file($courier);
                        $font->size(13);
                    }
                    $font->color('#F8FAFC');
                });
            }

            // Right ticket stub details
            $image->text(strtoupper($ticket->ticket_type), 590, 65, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(12);
                }
                $font->color('#94A3B8');
            });

            // Watermark / Brand on Stub
            $image->text('Powered by DeepNix software solutions', 570, 360, function ($font) use ($arial, $hasArial) {
                if ($hasArial) {
                    $font->file($arial);
                    $font->size(10);
                }
                $font->color('#64748B');
            });

            // White box background for QR code to ensure scannability on dark theme
            $image->drawRectangle(function ($draw) {
                $draw->at(585, 115);
                $draw->size(170, 170);
                $draw->background('#FFFFFF');
                $draw->border('#E2E8F0', 1);
            });

            // Place QR Code
            $qrImage = $manager->decode(Storage::path($qrPath));
            $qrImage->resize(160, 160);
            $image->insert($qrImage, 580, 120);
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
