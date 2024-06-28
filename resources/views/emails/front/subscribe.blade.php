@extends('emails.layouts.app')
{{-- @push('banner-section')
<tr>
    <td>
        <table width="600px" cellpadding="0" cellspacing="0" align="center" bgcolor="#fff"
            style="border-radius: 4px 4px 0px 0px;">
            <tr>
                <td height="24px"></td>
            </tr>
            <tr>
                <td style="text-align: center; margin: 0; font-size: 30px; line-height: 36px; font-weight: 700;"> <b
                        style="color: #E77F01;font-family: 'Open Sans', sans-serif; font-weight: 700;">Login
                        Credentials</b>
                </td>
            </tr>
            <tr>
                <td height="12px"></td>
            </tr>
        </table>
    </td>
</tr>
@endpush --}}
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
                                            Hello,
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="10px"></td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="text-align: left; font-size: 18px; line-height: 28px; color: #232323; font-family: 'Open Sans', sans-serif;">
                                            Please keep your login information confidential.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="text-align: left; font-size: 18px; line-height: 28px; color: #E77F01; font-family: 'Open Sans', sans-serif;">
                                            Regards, Stacked Up Pty Ltd
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