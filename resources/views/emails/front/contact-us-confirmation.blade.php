@extends('emails.layouts.app')
@push('banner-section')
<tr>
    <td>
        <table width="600px" cellpadding="0" cellspacing="0" align="center" bgcolor="#fff"
            style="border-radius: 4px 4px 0px 0px;">
            <tr>
                <td height="24px"></td>
            </tr>
            <tr>
                <td style="text-align: center; margin: 0; font-size: 30px; line-height: 36px; font-weight: 700;"> <b
                        style="color: #E77F01;font-family: 'Open Sans', sans-serif; font-weight: 700;">Thank You,</b>
                </td>
            </tr>
            <tr>
                <td height="12px"></td>
            </tr>
        </table>
    </td>
</tr>
@endpush
@section('main-content')
<tr>
    <td>
        <table border-collapse="collapse" border-spacing="0" width="100%" cellpadding="0" cellspacing="0"
            bgcolor="#F6F6F6">
            <tr>
                <td>
                    <table width="600px" align="center" cellpadding="0" cellspacing="0" bgcolor="#fff"
                        style="border-radius: 0px 0px 4px 4px;">
                        <tr>
                            <td>
                                <table width="90%" align="center" cellpadding="0" cellspacing="0" bgcolor="#fff">
                                    <tr>
                                        <td height="36px"></td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="margin: 0; font-size: 16px; line-height: 22px; font-weight: 700; color: #161616; font-family: 'Open Sans', sans-serif; ">
                                            Hi {{$data->full_name}},
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="10px"></td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="margin: 0px; text-align: left; font-size: 16px; line-height: 24px; color: #161616; font-family: 'Open Sans', sans-serif;">
                                            Thank you for contacting Us,We just Got your message.
                                            {{-- Please find your registration details. --}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="margin: 0px; text-align: left; font-size: 12px; line-height: 24px; color: #161616; font-family: 'Open Sans', sans-serif;">
                                            Your Enquiry Details:
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="24px"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                style="border-radius: 4px; border-top: 12px solid #232323;">
                                                <tr>
                                                    <td style="border:2px solid #ECECEC;">
                                                        <table width="90%" align="center" cellpadding="0"
                                                            cellspacing="0">
                                                            <tr>
                                                                <td height="32px"></td>
                                                            </tr>

                                                            <tr>
                                                                <td
                                                                    style="text-align: left; margin: 0; font-size: 16px; line-height: 26px;  color: #848484;">
                                                                    <b
                                                                        style="margin-bottom: 4px; font-weight: 300; font-family: 'Open Sans', sans-serif;">Full
                                                                        Name:</b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td
                                                                    style="text-align: left; color: #333333; margin: 0; font-size: 16px; line-height: 26px; font-family: 'Open Sans', sans-serif;">
                                                                    {{ $data->full_name ?? '--' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="32px"></td>
                                                            </tr>


                                                            <tr>
                                                                <td
                                                                    style="text-align: left; margin: 0; font-size: 16px; line-height: 26px;  color: #848484;">
                                                                    <b
                                                                        style="margin-bottom: 4px; font-weight: 300; font-family: 'Open Sans', sans-serif;">Email
                                                                        Address:</b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td
                                                                    style="text-align: left; color: #333333; margin: 0; font-size: 16px; line-height: 26px; font-family: 'Open Sans', sans-serif;">
                                                                    {{ $data->email_address ?? '--' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="32px"></td>
                                                            </tr>
                                                            <tr>
                                                                <td
                                                                    style="text-align: left; margin: 0; font-size: 16px; line-height: 26px;  color: #848484;">
                                                                    <b
                                                                        style="margin-bottom: 4px; font-weight: 300; font-family: 'Open Sans', sans-serif;">Message:</b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td
                                                                    style="text-align: left; color: #333333; margin: 0; font-size: 16px; line-height: 26px; font-family: 'Open Sans', sans-serif;">
                                                                    {{ $data->message ?? '--' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="32px"></td>
                                                            </tr>
                                                            @if ($data->image !== '')
                                                            <tr>
                                                                <td
                                                                    style="text-align: left; margin: 0; font-size: 16px; line-height: 26px;  color: #848484;">
                                                                    <b
                                                                        style="margin-bottom: 4px; font-weight: 300; font-family: 'Open Sans', sans-serif;">Attached
                                                                        Image:</b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td
                                                                    style="text-align: left; color: #333333; margin: 0; font-size: 16px; line-height: 26px; font-family: 'Open Sans', sans-serif;">
                                                                    <img src="{{generateURL($data->image)}}" alt="Image"
                                                                        height="140" width="160">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="32px"></td>
                                                            </tr>
                                                            @endif
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="42px"></td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="text-align: left; font-size: 18px; line-height: 28px; color: #232323; font-family: 'Open Sans', sans-serif;">
                                            Best Regards,
                                        </td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="text-align: left; font-size: 18px; font-weight: normal; line-height: 28px; color: #E77F01; font-family: 'Open Sans', sans-serif;">
                                            The {{ env('APP_NAME') }} Team
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="52px"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection