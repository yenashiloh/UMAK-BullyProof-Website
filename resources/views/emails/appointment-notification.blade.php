<!DOCTYPE html>
<html>

<head>
    <title>Appointment Notification</title>
</head>
<style>
    .notice {
        margin-top: 20px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 4px;
        font-size: 0.9em;
    }

    a {
        color: #1a3f8e;
        text-decoration: none;
    }
</style>

<body>
<body>
    <p>{!! $emailContent !!}</p>
    <p>Date: <strong>{{ $appointmentDate }}</strong></p>
    <p>Time: <strong>{{ $appointmentStartTime }} to {{ $appointmentEndTime }}</strong></p>

    <div class="container">
        <div class="info-section"
            style="border: 1px solid black; padding: 20px; border-radius: 8px; background-color: transparent;">
            <h3>CENTER FOR STUDENT FORMATION AND DISCIPLINE (CSFD)</h3>
            <p>5th Admin Building, University of Makati</p>
            <p>Tel. No.: 8883-1875</p>
            <p>Email: <a href="mailto:csfd@umak.edu.ph"
                    style="text-decoration: underline; color: #007bff;">csfd@umak.edu.ph</a></p>
            <p>Facebook: <a href="#"
                    style="text-decoration: underline; color: #007bff;">www.facebook.com/UMakPSD</a></p>
            <div class="notice">
                <strong>Confidentiality Notice:</strong>
                <p>Please immediately inform the sender and the data protection/privacy officer if this message is
                    wrongfully received/sent/forwarded, reproduced, or any other unauthorized access to this message.
                    <strong>Please email the Data Protection Officer, University of Makati at
                        <a href="mailto:dprms@umak.edu.ph"
                            style="text-decoration: underline; color: #007bff;">dprms@umak.edu.ph</a>
                    </strong>
                </p>
            </div>
        </div>
    </div>
    {{-- <p>In line with this, this office would like to invite you for a preliminary/clarificatory conference on {{ $appointmentDate }} from  {{ $appointmentStartTime }} to {{ $appointmentEndTime }} for further clarification and/or discussion on this matter.</p>
    <p>Kindly confirm your availability through this email today.</p> --}}


    {{-- <p>This is still related to your administrative/community service concern/s rendered at the Inclusive Education -
        Gender and Development (IE-GAD) office. Please be informed the we have
        received a report forwarded at this office dated today, regarding an alleged rude and disrespectful behavior
        received a re    <p>{!! $emailContent !!}</p>
    <p>In line with this, this office would like to invite you for a preliminary/clarificatory conference on {{ $appointmentDate }} from  {{ $appointmentStartTime }} to {{ $appointmentEndTime }} for further clarification and/or discussion on this matter.</p>
    <p>Kindly confirm your availability through this email today.</p>


    {{-- <p>This is still related to your administrative/community service concern/s rendered at the Inclusive Education -
        Gender and Development (IE-GAD) office. Please be informed the we have
        received a report forwarded at this office dated today, regarding an alleged rude and disrespectful behavior
        received a re    <p>{!! $emailContent !!}</p>
    <p>In line with this, this office would like to invite you for a preliminary/clarificatory conference on {{ $appointmentDate }} from  {{ $appointmentStartTime }} to {{ $appointmentEndTime }} for further clarification and/or discussion on this matter.</p>
    <p>Kindly confirm your availability through this email today.</p>


    {{-- <p>This is still related to your administrative/community service concern/s rendered at the Inclusive Education -
        Gender and Development (IE-GAD) office. Please be informed the we have
        received a report forwarded at this office dated today, regarding an alleged rude and disrespectful behavior
        towards non-teaching staff including an alleged disclosure of confidential
        information under the Violation of the Data Privacy Act of 2012, both violations of the university Student
       
        Handbook, to wit:
    </p>
    </p>
    </p>


    <p>4.2.17 Grave act of disrespect that tends to malign the University officials, faculty members or
        <strong>administrative non-teaching staff.</strong> </p>

    <p>4.2.35 Violation of the Anti-Bullying Act of 2013 and/or Cybercrime Prevention Act of 2012 and other
        similar/related laws</p>

    <p>*(Please see pp. 84-85 of the Student Handbook 2021 Edition)</p>

    <p>In line with this, this office would like to invite you for a preliminary/clarificatory conference on

        <strong>{{ $appointmentDate }} from {{ $appointmentStartTime }} to {{ $appointmentEndTime }}</strong>
        for further clarification and/or discussion on this matter.
    </p>

    <p><strong>You are advised to bring your parent's and/or guardian/s with you</strong></p>

    <p><strong>Please be guidance that your parent's/guardian's physical appearance is relevant and/or important to
            address this issue and to proceed with the proper process and procedures.</strong></p>

    <p><str<str<strong>In case your parent/s and/or guardian/s are not available to attend, please provide a letter of consent
                    from your parent/s and/or guardian/s affixed wit their physical signature and valid I.D</strong></p>



    <p>KindKindKindly confirm your availability through this email today.</p>

    <strong>(Inability to confirm your attendance through this email will be automatically rescheduled)</strong>

    <p>Thank you</p>

    <br>
 --}}


    {{-- <p>Complainant:</p>
    <p>{{ $appointmentData['complainant_name'] }}</p>

    <p>Student/s concerned:</p>
    <p>{{ $appointmentData['respondent_name'] }}</p>

    <p>Time:</p>
    <p></p>

    <p>Date:</p>
    <p></p>

    <p>Thank you for your attention to this matter.</p> --}}
</body>

</html>
