<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Modern Ticket Confirmation</title>
  <style>
    body {
      background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
      color: #334155;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      margin: 0;
      padding: 0;
      width: 100% !important;
    }
    table {
      border-collapse: collapse;
    }
    img {
      border: 0;
      height: auto;
      line-height: 100%;
      outline: none;
      text-decoration: none;
    }
  </style>
</head>
<body style="background-color: #0F172A; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0;">
  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #0F172A; padding: 40px 10px;">
    <tr>
      <td align="center">
        
        <!-- Ticket Outer Card -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 480px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
          
          <!-- TOP HEADER SECTION WITH GRADIENT -->
          <tr>
            <td style="background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); padding: 30px 32px; text-align: left;">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td>
                    <h1 style="margin: 0; font-size: 22px; color: #ffffff; font-weight: 700; letter-spacing: -0.5px;">{{ strtoupper($eventName) }}</h1>
                    <p style="margin: 8px 0 0 0; color: rgba(255,255,255,0.8); font-size: 13px; font-weight: 500; letter-spacing: 0.5px; text-transform: uppercase;">
                      @if($isResend)
                        RE-ISSUED E-TICKET
                      @else
                        DIGITAL E-TICKET
                      @endif
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- TICKET BODY -->
          <tr>
            <td style="padding: 32px;">
              
              <!-- Location & Date columns -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                <tr>
                  <td width="50%" style="vertical-align: top; padding-right: 15px;">
                    <span style="font-size: 11px; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.8px; display: block; margin-bottom: 6px;">Venue</span>
                    <span style="font-size: 14px; color: #0F172A; font-weight: 700; line-height: 1.4; display: block;">
                      {{ strtoupper($venue) }}
                    </span>
                  </td>
                  <td width="50%" style="vertical-align: top;">
                    <span style="font-size: 11px; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.8px; display: block; margin-bottom: 6px;">Date & Time</span>
                    <span style="font-size: 14px; color: #0F172A; font-weight: 700; line-height: 1.4; display: block;">
                      {{ strtoupper($startsAt) }}
                    </span>
                  </td>
                </tr>
              </table>

              <!-- Ticket Info -->
              <div style="background-color: #F8FAFC; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 13px;">
                  <tr style="height: 28px;">
                    <td style="color: #64748B; font-weight: 500;">Attendee Name</td>
                    <td align="right" style="font-weight: 700; color: #0F172A;">{{ strtoupper($name) }}</td>
                  </tr>
                  <tr style="height: 28px;">
                    <td style="color: #64748B; font-weight: 500;">Ticket Type</td>
                    <td align="right" style="font-weight: 700; color: #0F172A;">{{ strtoupper($ticketType) }}</td>
                  </tr>
                  <tr style="height: 28px;">
                    <td style="color: #64748B; font-weight: 500;">Quantity</td>
                    <td align="right" style="font-weight: 700; color: #0F172A;">{{ $quantity ?? 1 }}</td>
                  </tr>
                  <tr style="height: 28px;">
                    <td style="color: #64748B; font-weight: 500;">Amount Paid</td>
                    <td align="right" style="font-weight: 800; color: #3B82F6;">
                      {{ $price > 0 ? 'LKR ' . number_format($price, 2) : 'FREE' }}
                    </td>
                  </tr>
                  @if(!empty($seatNumber))
                  <tr style="height: 28px;">
                    <td style="color: #64748B; font-weight: 500;">Seat / Zone</td>
                    <td align="right" style="font-weight: 700; color: #0F172A;">{{ strtoupper($seatNumber) }}</td>
                  </tr>
                  @endif
                </table>
              </div>

              <!-- QR Code Section -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td align="center">
                    <p style="margin: 0 0 16px 0; font-size: 12px; color: #64748B; font-weight: 500;">Present this QR code at the entrance</p>
                    <div style="background-color: #ffffff; padding: 16px; display: inline-block; border: 2px solid #E2E8F0; border-radius: 16px; margin-bottom: 12px;">
                      <img src="{{ $message->embed(Illuminate\Support\Facades\Storage::path('public/qrcodes/' . $token . '.png')) }}" width="160" height="160" alt="QR Code" style="display: block; margin: 0 auto; border-radius: 8px;">
                    </div>
                    <div style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 12px; color: #94A3B8; font-weight: 600; letter-spacing: 1px;">
                      {{ substr($token, 0, 16) }}
                    </div>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- FOOTER / WATERMARK -->
          <tr>
            <td style="background-color: #F1F5F9; padding: 16px; text-align: center; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
              <span style="font-size: 10px; font-weight: 600; color: #CBD5E1; text-transform: uppercase; letter-spacing: 1px;">
                Powered by DeepNix software solutions
              </span>
            </td>
          </tr>

        </table>
        
        <!-- Outside Footer -->
        <div style="margin-top: 24px; text-align: center; font-size: 11px; color: #64748B;">
          Please do not share this ticket with anyone else.
        </div>

      </td>
    </tr>
  </table>
</body>
</html>
