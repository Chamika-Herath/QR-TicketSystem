<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Ticket Confirmation</title>
  <style>
    body {
      background-color: #4C33A3;
      color: #1B2430;
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
<body style="background-color: #4C33A3; font-family: sans-serif; margin: 0; padding: 0;">
  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #4C33A3; padding: 40px 10px;">
    <tr>
      <td align="center">
        
        <!-- Ticket Outer Card -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 460px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
          
          <!-- TOP TICKET PART -->
          <tr>
            <td style="padding: 32px 32px 20px 32px;">
              
              <!-- Brand Header -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px; border-bottom: 1px solid #F1F5F9; padding-bottom: 15px;">
                <tr>
                  <td>
                    <span style="font-family: sans-serif; font-size: 16px; font-weight: 800; color: #4C33A3; letter-spacing: 0.5px; text-transform: uppercase;">
                      TICKET // COUNTER
                    </span>
                  </td>
                  <td align="right">
                    <span style="font-family: monospace; font-size: 10px; font-weight: bold; color: #8A8577; text-transform: uppercase; letter-spacing: 1px;">
                      @if($isResend)
                        [ COPY ]
                      @else
                        [ PASS ]
                      @endif
                    </span>
                  </td>
                </tr>
              </table>

              <!-- Location & Date columns -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                <tr>
                  <td width="50%" style="vertical-align: top; padding-right: 15px;">
                    <span style="font-family: sans-serif; font-size: 10px; font-weight: 800; color: #4C33A3; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">Location</span>
                    <span style="font-family: sans-serif; font-size: 12px; color: #1B2430; font-weight: bold; line-height: 1.4; display: block;">
                      {{ strtoupper($venue) }}
                    </span>
                  </td>
                  <td width="50%" style="vertical-align: top;">
                    <span style="font-family: sans-serif; font-size: 10px; font-weight: 800; color: #4C33A3; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">Date/Time</span>
                    <span style="font-family: sans-serif; font-size: 12px; color: #1B2430; font-weight: bold; line-height: 1.4; display: block;">
                      {{ strtoupper($startsAt) }}
                    </span>
                  </td>
                </tr>
              </table>

              <!-- Ticket Info table -->
              <div style="border-top: 1px solid #F1F5F9; padding-top: 16px;">
                <span style="font-family: sans-serif; font-size: 10px; font-weight: 800; color: #4C33A3; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 10px;">Ticket Info</span>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: sans-serif; font-size: 12px;">
                  <tr style="height: 26px;">
                    <td style="color: #64748B;">Name</td>
                    <td align="right" style="font-weight: bold; color: #1B2430;">{{ strtoupper($name) }}</td>
                  </tr>
                  <tr style="height: 26px;">
                    <td style="color: #64748B;">Type</td>
                    <td align="right" style="font-weight: bold; color: #1B2430;">{{ strtoupper($ticketType) }}</td>
                  </tr>
                  <tr style="height: 26px;">
                    <td style="color: #64748B;">Quantity</td>
                    <td align="right" style="font-weight: bold; color: #1B2430;">{{ $quantity ?? 1 }}</td>
                  </tr>
                  <tr style="height: 26px;">
                    <td style="color: #64748B;">Price</td>
                    <td align="right" style="font-weight: bold; color: #4C33A3;">
                      {{ $price > 0 ? 'LKR ' . number_format($price, 2) : 'FREE' }}
                    </td>
                  </tr>
                  @if(!empty($seatNumber))
                  <tr style="height: 26px;">
                    <td style="color: #64748B;">Seat/Stub</td>
                    <td align="right" style="font-weight: bold; color: #1B2430;">{{ strtoupper($seatNumber) }}</td>
                  </tr>
                  @endif
                </table>
              </div>

            </td>
          </tr>

          <!-- PERFORATION SPLIT ROW WITH CUTOUT NOTCHES -->
          <tr>
            <td>
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <!-- Left Notch Cutout -->
                  <td width="12" style="background-color: #4C33A3; border-top-right-radius: 12px; border-bottom-right-radius: 12px; height: 24px;"></td>
                  <!-- Dashed Line -->
                  <td style="border-bottom: 2px dashed #E2E8F0; height: 12px; vertical-align: middle;"></td>
                  <!-- Right Notch Cutout -->
                  <td width="12" style="background-color: #4C33A3; border-top-left-radius: 12px; border-bottom-left-radius: 12px; height: 24px;"></td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- BOTTOM TICKET STUB PART -->
          <tr>
            <td style="padding: 24px 32px 36px 32px; text-align: center;">
              <h3 style="font-family: sans-serif; font-size: 16px; font-weight: bold; margin: 0 0 8px 0; color: #1B2430; line-height: 1.3;">
                {{ strtoupper($eventName) }}
              </h3>
              <div style="font-family: sans-serif; font-size: 11px; color: #64748B; margin-bottom: 16px; letter-spacing: 0.5px;">
                {{ strtoupper($ticketType) }}
              </div>
              
              <!-- Centered Larger QR Code -->
              <div style="background-color: #ffffff; padding: 12px; display: inline-block; border: 1px solid #E2E8F0; border-radius: 8px; margin-bottom: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.04);">
                <img src="{{ $message->embed(Illuminate\Support\Facades\Storage::path('public/qrcodes/' . $token . '.png')) }}" width="160" height="160" alt="QR Code" style="display: block; margin: 0 auto;">
              </div>

              <div style="font-family: monospace; font-size: 11px; color: #C4471F; font-weight: bold; margin-bottom: 4px;">
                TICKET #: {{ substr($token, 0, 16) }}
              </div>
              @if(!empty($seatNumber))
              <div style="font-family: monospace; font-size: 11px; color: #1B2430; font-weight: bold;">
                SEAT/STUB: {{ strtoupper($seatNumber) }}
              </div>
              @endif
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>
</body>
</html>
