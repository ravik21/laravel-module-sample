<!DOCTYPE HTMLDTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
   <head>
      <!--[if gte mso 9]>
      <xml>
         <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
         </o:OfficeDocumentSettings>
      </xml>
      <![endif]-->
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta name="viewport" content="width=device-width">
      <!--[if !mso]><!-->
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!--<![endif]-->
      <title>Monthly Digest Email</title>
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900">
      <style type="text/css" id="media-query">
         body {
           margin: 0;
           padding: 0;
         }
         a, h1, h2, h3, h4, h5, p, span{
           font-family:'Nunito', Helvetica Neue, Helvetica, Arial, sans-serif;
         }

         table, tr, td {
         vertical-align: top;
         border-collapse: collapse; }
         .ie-browser table, .mso-container table {
         table-layout: fixed; }
         * {
         line-height: inherit; }
         a[x-apple-data-detectors=true] {
         color: inherit !important;
         text-decoration: none !important; }
         [owa] .img-container div, [owa] .img-container button {
         display: block !important; }
         [owa] .fullwidth button {
         width: 100% !important; }
         [owa] .block-grid .col {
         display: table-cell;
         float: none !important;
         vertical-align: top; }
      </style>
   </head>
   <body class="clean-body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #ececec">
      <style type="text/css" id="media-query-bodytag">

      </style>
      <table style="box-sizing:border-box;font-size:14px;width:100%;background:rgb(246,246,246);margin:0px;padding:0px">
         <tbody>
            <tr style="box-sizing:border-box;font-size:14px;margin:0px;padding:0px">
               <td style="box-sizing:border-box;font-size:14px;vertical-align:top;margin:0px;padding:0px" valign="top"></td>
               <td width="600" style="box-sizing:border-box;font-size:14px;vertical-align:top;margin:0px auto;padding:0px;display:block;max-width:600px;clear:both" valign="top">
                  <div style="box-sizing:border-box;font-size:14px;max-width:600px;display:block;margin:0px auto;padding:20px">
                     <table width="100%" cellpadding="0" cellspacing="0" style="box-sizing:border-box;font-size:14px;border-radius:3px;background:rgb(255,255,255);margin:0px;padding:0px;border:1px solid rgb(233,233,233)">
                        <tbody>
                           <tr style="box-sizing:border-box;font-size:14px;margin:0px;padding:0px">
                              <td style="box-sizing:border-box;font-size:14px;vertical-align:top;margin:0px" valign="top">
                                 <table width="100%" cellpadding="0" cellspacing="0" style="box-sizing:border-box;font-size:14px;margin:0px;padding:0px">
                                    <tbody>
                                       <tr style="box-sizing:border-box;font-size:14px;margin:0px;padding:0px;">
                                          <td style="box-sizing:border-box;font-size:20px;font-weight:bold;vertical-align:top;margin:0px;padding:15px 0px 15px;" valign="top">
                                             <a href="{{config('app.web_url')}}" target="_blank">
                                               <img src="{{ asset('/themes/backend/img/logo.svg') }}" alt="Alegrant Logo" style="margin:0px auto;display:block;height:75px;">
                                             </a>
                                          </td>
                                       </tr>
                                       <tr style="box-sizing:border-box;font-size:14px;margin:0px;padding:0px;background:#286BA2;">
                                          <td style="box-sizing:border-box;font-size:14px;vertical-align:top;margin:0px" valign="top">
                                             <h1 style="color:#fff;text-transform:uppercase;text-align:center;margin:25px 0px 0px">Recommended for you</h1>
                                             <p style="color:#fff;font-size:16px;text-align:center;line-height:20px;width:300px;margin:0px auto 25px">
                                                We've selected some articles we think you might be interested in.
                                             </p>
                                          </td>
                                       </tr>
                                       <tr style="background:rgb(213,217,229);box-sizing:border-box;font-size:14px;margin:0px;padding:0px">
                                          <td style="box-sizing:border-box;font-size:14px;vertical-align:top;margin:0px;padding:40px 30px 0px" valign="top">
                                            @foreach($feed as $item)
                                              @php
                                                if (isset($item['mediaFile'])) {
                                                  $imageUrl = $item['mediaFile']['is_image'] ? $item['mediaFile']['medium_thumb'] : (isset($item['mediaFile']['video_thumb']) ? $item['mediaFile']['video_thumb'] : '');
                                                } else {
                                                  $imageUrl = null;
                                                }
                                              @endphp
                                              <div style="width:219px;height:319px;float:left;min-height:1px;padding:0px 15px;margin-bottom:30px">
                                                <a href="{{ config('app.web_url') . '/landing/' . strtolower($item['feed_key']) . '/' . $item['id'] }}" style="display:block;text-decoration:none" target="_blank">
                                                  <div style="background-image: url({{$imageUrl}});background-repeat: no-repeat;background-size: cover;background-position: 50%;height: 150px;position: relative;margin-bottom:7px;"></div>
                                                  <div style="padding:10px 15px;height:91px;overflow:hidden;background-color:rgb(255,255,255);position:relative;">
                                                    <h2 style="text-transform:uppercase;margin:0px 0px 8px;line-height:1.2;display:block;font-size:16px;color:#286ba2;font-weight:600">
                                                      {{ strtoupper(str_limit($item['title'], 50)) }}
                                                    </h2>
                                                    <p style="font-size:13px;color:#2e3034;margin:0px;position:absolute;bottom:15px;">
                                                      @if(strtolower($item['feed_key']) != 'event')
                                                        {{ date('d/m/Y', strtotime($item['published_at'])) }}
                                                      @else
                                                        {{ $item['start_date'] }} {{ strtolower($item['start_time']) }} <br>
                                                        to {{ $item['end_date'] }} {{ strtolower($item['end_time']) }} <br>
                                                        {{ $item['address_line_1'] }}
                                                      @endif
                                                    </p>
                                                  </div>
                                                  <div style="background:#286ba2;padding:10px 15px;border-radius:0px 0px 4px 4px;max-height:50px">
                                                    <p style="font-size:14px;color:#fff;font-weight:600;font-style:italic;margin:0px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                                        <img src="{{ $item['author_avatar_url'] }}" alt="avatar" style="border-radius:50%;height:30px;margin-right:5px;vertical-align:middle;width:30px;border:1px solid #fff;"title="{{ $item['author'] }}">
                                                        by {{ str_limit($item['author'], 12, '..') }}
                                                    </p>
                                                  </div>
                                                </a>
                                              </div>
                                              @endforeach
                                          </td>
                                       </tr>
                                       <tr style="background:rgb(213,217,229)">
                                          <td style="box-sizing:border-box;vertical-align:top;margin:0px;padding:0px 45px 40px">
                                             <p style="color:rgb(102,91,99);font-size:16px;float:left;margin:7px 0px 0px">Nothing of interest? Why not visit the site</p>
                                             <a href="{{config('app.web_url') . '/articles-and-events'}}" style="margin-top:5px;padding:3px 20px;color:#286BA2;font-weight:bold;border:3px solid rgb(185,191,207);border-radius:6px;background:rgb(255,255,255);font-size:14px;display:inline-block;text-decoration:none;line-height:1.42857;float:right" target="_blank">
                                             View more
                                             </a>
                                          </td>
                                       </tr>
                                       <tr style="box-sizing:border-box;margin:0px;padding:0px">
                                          <td style="box-sizing:border-box;font-size:14px;vertical-align:top;margin:0px;padding:30px 45px" valign="top">
                                             <a href="{{ route('digest.unsubscribe', $user['id']) }}" target="_blank" rel="noopener" style="float:right;color:rgb(90,90,98);margin-top:10px" target="_blank">Unsubscribe</a>
                                             <div>
                                                <p style="color:rgb(90,90,98);margin:0px 0px 5px;font-size:14px;line-height:1;font-family:Lato,arial,sans-serif">Thanks</p>
                                                <p style="color:rgb(90,90,98);font-weight:bold;margin:0px;font-size:14px;line-height:1;font-family:Lato,arial,sans-serif">The Alegrant Team</p>
                                             </div>
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </td>
            </tr>
         </tbody>
      </table>
      <!--[if (mso)|(IE)]></div><![endif]-->
   </body>
</html>
