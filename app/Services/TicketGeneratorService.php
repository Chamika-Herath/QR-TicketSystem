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
            // Create a gorgeous canvas from scratch (800x400) if no template is uploaded
            $image = $manager->createImage(800, 400);
            $image->fill('#F7F3EC'); // warm paper

            // Draw border
            $image->drawRectangle(function ($draw) {
                $draw->at(15, 15);
                $draw->size(770, 370);
                $draw->background('#00000000');
                $draw->border('#1B2430', 2);
            });

            // Draw vertical divider line
            $image->drawLine(function ($draw) {
                $draw->from(540, 15);
                $draw->to(540, 385);
                $draw->color('#1B2430');
                $draw->width(2);
            });

            // Draw punch hole circles (notches)
            $image->drawCircle(function ($draw) {
                $draw->at(540, 15);
                $draw->radius(15);
                $draw->background('#ffffff');
                $draw->border('#1B2430', 2);
            });

            $image->drawCircle(function ($draw) {
                $draw->at(540, 385);
                $draw->radius(15);
                $draw->background('#ffffff');
                $draw->border('#1B2430', 2);
            });

            // Draw decorative lines
            $image->drawLine(function ($draw) {
                $draw->from(50, 95);
                $draw->to(500, 95);
                $draw->color('#E8E1D3');
                $draw->width(1);
            });

            $image->drawLine(function ($draw) {
                $draw->from(50, 320);
                $draw->to(500, 320);
                $draw->color('#E8E1D3');
                $draw->width(1);
            });

            // Set up font files with fallbacks
            $courier = '/System/Library/Fonts/Supplemental/Courier New.ttf';
            $arial = '/System/Library/Fonts/Supplemental/Arial.ttf';
            $hasCourier = file_exists($courier);
            $hasArial = file_exists($arial);

            // Write event details onto the custom paper ticket
            $image->text(strtoupper($event->name), 50, 65, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(20);
                }
                $font->color('#1B2430');
            });

            $image->text(strtoupper($attendee->name), 50, 140, function ($font) use ($arial, $hasArial) {
                if ($hasArial) {
                    $font->file($arial);
                    $font->size(28);
                }
                $font->color('#C4471F'); // stamp orange
            });

            // VENUE
            $image->text('VENUE:', 50, 200, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#8A8577');
            });
            $image->text(strtoupper($event->venue), 140, 200, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#1B2430');
            });

            // DATE
            $image->text('DATE/TIME:', 50, 240, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#8A8577');
            });
            $image->text(strtoupper($event->starts_at->format('M d, Y h:i A')), 140, 240, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#1B2430');
            });

            // REF
            $image->text('TICKET REF:', 50, 280, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#8A8577');
            });
            $image->text(strtoupper(substr($ticket->token, 0, 16)), 180, 280, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#C4471F'); // stamp orange
            });

            if ($ticket->seat_number) {
                $image->text('SEAT/STUB:', 50, 320, function ($font) use ($courier, $hasCourier) {
                    if ($hasCourier) {
                        $font->file($courier);
                        $font->size(13);
                    }
                    $font->color('#8A8577');
                });
                $image->text(strtoupper($ticket->seat_number), 180, 320, function ($font) use ($courier, $hasCourier) {
                    if ($hasCourier) {
                        $font->file($courier);
                        $font->size(13);
                    }
                    $font->color('#1B2430');
                });
            }

            // Right ticket stub details - display ticket type dynamically
            $image->text(strtoupper($ticket->ticket_type), 580, 65, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(12);
                }
                $font->color('#8A8577');
            });

            $image->text('[ SCAN ENTRY ]', 580, 340, function ($font) use ($courier, $hasCourier) {
                if ($hasCourier) {
                    $font->file($courier);
                    $font->size(13);
                }
                $font->color('#C4471F');
            });

            // Draw border around QR code area on the right stub
            $image->drawRectangle(function ($draw) {
                $draw->at(577, 117);
                $draw->size(166, 166);
                $draw->background('#00000000');
                $draw->border('#1B2430', 1);
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
