@extends('layouts.legal')

@section('title')
    {{ trans('user::auth.cookies') }} | @parent
@stop

@section('content')
    <div class="col-sm-12">
        <h2>Cookie Policy</h2>

        <p class="sub-heading">What is a cookie?</p>

        <p>Most websites you visit will use cookies in order to improve your user experience by enabling that website to ‘remember’ you, either for the duration of your visit (using a ‘session cookie’) or for repeat visits (using a ‘persistent cookie’).</p>

        <p>Cookies are simple ’text files’ which you can read using the Notebook program on your own PC. Typically, they contain two pieces of information: a site name and unique user ID.</p>

        <p>When you visit a site that uses cookies for the first time, a cookie is downloaded onto your PC. The next time you visit that (or partner) site, your PC checks to see if it has a cookie that is relevant (that is, one containing the site name).</p>

        <p>The site then ’knows’ that you have been there before, and in some cases, tailors content to take account of that fact. For instance, it can be helpful to vary content according to whether this is your first ever visit to a site – or your 71st. We use cookies to identify members interacting with our website.</p>

        <p>Cookies used on our website:</p>

        <div id="cookie-table">
           <table>
               <thead>
               <tr>
                   <th>Name</th>
                   <th>Essential to the users</th>
                   <th>Supplied from</th>
                   <th>Expiry length</th>
                   <th>Use</th>
               </tr>
               </thead>
               <tbody>
                   <tr>
                       <td>XSRF-TOKEN</td>
                       <td>YES</td>
                       <td>.britishcycling.lassoplatform.com</td>
                       <td>End of Session</td>
                       <td>This cookie is written to help with site security in preventing Cross-Site Request Forgery attacks.</td>
                   </tr>
                   <tr>
                       <td>laravel_session</td>
                       <td>YES</td>
                       <td>.britishcycling.lassoplatform.com</td>
                       <td>End of Session</td>
                       <td>This is used to hold information about your current visit with us. This cookie is essential to the security and functionality of the website.</td>
                   </tr>
               </tbody>
           </table>
        </div>
    </div>
@stop
