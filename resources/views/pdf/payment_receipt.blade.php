<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Payment has been received</title>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="4">
                <table>
                    <tr>
                        <td class="title">
                            <img src="{{public_path("/images/defaults/logo.png")}}"
                                 style="width:100%; max-width:262px;">
                        </td>
                        <td></td>
                        <td></td>

                        <td>
                            Receipt: #{{$payment_details['payment_details']->mpesaReceiptNumber}}<br>
                            Created: {{  date("d M Y", strtotime($payment_details['payment_details']->created_at)) }}
                            <br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
        <tr>
            <td>
                To<br>
                Adklout, Ltd.<br>
                Nairobi<br>
                Kenya
            </td>
            <td></td>
            <td></td>
            <td>
                From <br>
                @if(isset($payment_details['user']->first_name))
                    {{ $payment_details['user']->first_name }}
                @else
                    {{ $payment_details['user']->user_name }}
                @endif

                {{' '. $payment_details['user']->last_name }}
                <br>{{ $payment_details['user']->email }}<br>
            </td>
        </tr>

        <br>


        <tr class="heading">
            <td>
                Payment Method
            </td>
            <td>
                Code
            </td>

            <td>
                Amount
            </td>
            <td>
                Number
            </td>
        </tr>

        <tr class="details">
            <td>
                {{ $payment_details['payment_method'] }}
            </td>
            <td>
                {{$payment_details['payment_details']->mpesaReceiptNumber}}
            </td>

            <td>
                {{$payment_details['payment_details']->amount}}
            </td>
            <td>
                {{$payment_details['payment_details']->phoneNumber}}
            </td>
        </tr>

        <tr class="total">
            <td></td>
            <td></td>
            <td></td>

            <td>
                <br>
                <br>
                Total Paid: Kes {{$payment_details['payment_details']->amount}}

            </td>
        </tr>
    </table>
</div>
</body>
</html>
