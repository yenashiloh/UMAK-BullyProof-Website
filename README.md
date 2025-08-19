<h1>BullyProof Web Application</h1>

<p><strong>University of Makati</strong> recognizes the need for comprehensive administrative oversight in combating cyberbullying within its academic community. As incidents require thorough investigation and careful management, the university administration demands robust tools that can efficiently handle case management, user oversight, and communication coordination while maintaining institutional standards and protocols.</p>

<p>The university's commitment to creating a safe digital environment extends beyond reporting mechanisms to include sophisticated administrative controls, requiring a comprehensive web-based platform that can manage incident workflows, coordinate between stakeholders, and provide detailed analytics for informed decision-making across all departments and administrative levels.</p>

<p>This comprehensive web application was developed as the administrative counterpart to the BullyProof mobile app, designed primarily for admin and director-level management of cyberbullying prevention and response operations. The platform integrates machine learning analysis using a <strong>Logistic Regression Algorithm</strong> to automatically assess and categorize incident reports, providing administrators with intelligent insights and percentage-based cyberbullying classifications for enhanced decision-making.</p>

<h2>🖥️ <strong>PROJECT OVERVIEW</strong></h2>
<br>

<p><strong>BullyProof Web Application</strong> is a Laravel-based administrative platform that enables directors and administrators at the University of Makati to manage, investigate, and resolve cyberbullying incidents reported through the mobile application. The system incorporates advanced analytics, automated email management, appointment scheduling, and comprehensive user oversight capabilities.</p>

<h2>🎯 <strong>PROJECT OBJECTIVES</strong></h2>
<br>

<ul>
    <li>Provide comprehensive administrative oversight of cyberbullying incidents</li>
    <li>Streamline case management and investigation workflows</li>
    <li>Enable intelligent incident classification using machine learning</li>
    <li>Facilitate communication between all stakeholders</li>
    <li>Maintain detailed analytics and reporting capabilities</li>
    <li>Ensure secure and controlled access to sensitive information</li>
</ul>

<h2>✨ <strong>KEY FEATURES</strong></h2>
<br>

<h3>📊 1. Administrative Dashboard</h3>
<ul>
    <li>Comprehensive statistics overview including:
        <ul>
            <li>Total number of registered users</li>
            <li>Total incidents reported from mobile app</li>
            <li>Reports currently under investigation</li>
            <li>Successfully resolved cases</li>
            <li>Reports awaiting administrative confirmation</li>
        </ul>
    </li>
    <li>Monthly incident reports with trend analysis</li>
    <li>Interactive bar charts displaying incidents by platform/department</li>
    <li>Real-time data visualization and analytics</li>
</ul>

<h3>📧 2. Email Content Management</h3>
<ul>
    <li>Custom email template creation for different stakeholders:
        <ul>
            <li>Complainants (report submitters)</li>
            <li>Complainees (reported individuals)</li>
            <li>Respective departments and supervisors</li>
        </ul>
    </li>
    <li>Automated notification systems for:
        <ul>
            <li>Scheduled appointments</li>
            <li>Rescheduled meetings</li>
            <li>Canceled appointments</li>
            <li>Case status updates</li>
        </ul>
    </li>
    <li>Template customization with dynamic content insertion</li>
</ul>

<h3>📅 3. Appointment Management</h3>
<ul>
    <li>Comprehensive scheduling system allowing:
        <ul>
            <li>Meeting setup between complainants and complainees</li>
            <li>Department representative inclusion</li>
            <li>Flexible date and time configuration</li>
            <li>Start time and end time specification</li>
        </ul>
    </li>
    <li>Appointment status monitoring and updates</li>
    <li>Automatic email notifications to all participants</li>
    <li>Calendar integration and conflict detection</li>
</ul>

<h3>👥 4. User Management</h3>
<ul>
    <li>Complete user account oversight including:
        <ul>
            <li>Account activation and deactivation capabilities</li>
            <li>User behavior monitoring</li>
            <li>Application misuse detection and prevention</li>
            <li>Security breach response protocols</li>
        </ul>
    </li>
    <li>Role-based access control for different administrative levels</li>
    <li>Audit trails for all user management actions</li>
</ul>

<h2>🤖 <strong>MACHINE LEARNING INTEGRATION</strong></h2>
<br>

<p>The web application incorporates a <strong>Logistic Regression Algorithm</strong> that:</p>
<ul>
    <li>Analyzes incident reports submitted from the mobile application</li>
    <li>Calculates cyberbullying probability percentages for each report</li>
    <li>Provides intelligent classification to assist administrative decision-making</li>
    <li>Supports both English and Tagalog content analysis</li>
    <li>Generates detailed analytical reports for trend identification</li>
    <li>Enables data-driven case prioritization and resource allocation</li>
</ul>

<h2>🛠 <strong>TECHNOLOGY STACK</strong></h2>
<br>

<h3>Frontend Technologies</h3>
<ul>
    <li><strong>HTML5</strong> - Semantic markup and structure</li>
    <li><strong>CSS3</strong> - Custom styling and responsive design</li>
    <li><strong>SCSS</strong> - CSS preprocessor for enhanced styling capabilities</li>
    <li><strong>Bootstrap</strong> - Responsive UI framework and components</li>
    <li><strong>JavaScript</strong> - Interactive functionality and client-side logic</li>
</ul>

<h3>Backend Technologies</h3>
<ul>
    <li><strong>Laravel</strong> - PHP web application framework</li>
    <li><strong>Python</strong> - Machine learning implementation and data processing</li>
</ul>

<h3>Additional Technologies</h3>
<ul>
    <li><strong>MongoDB</strong> - Database management</li>
    <li><strong>Chart.js/D3.js</strong> - Data visualization and analytics</li>
    <li><strong>SMTP</strong> - Email delivery system</li>
</ul>

<h2>📁 <strong>PROJECT STRUCTURE</strong></h2>
<br>

<pre>
bullyproof-web/
├── app/                     # Laravel application logic
│   ├── Http/               # Controllers and middleware
│   ├── Models/             # Eloquent models
│   ├── Mail/               # Email templates and classes
│   └── Services/           # Business logic services
├── resources/              # Frontend resources
│   ├── views/              # Blade templates
│   ├── css/                # Custom stylesheets
│   └── js/                 # JavaScript files
├── public/                 # Public assets
│   ├── css/                # Compiled CSS
│   ├── js/                 # Compiled JavaScript
│   └── images/             # Static images
├── database/               # Database files
│   ├── migrations/         # Database schema
│   └── seeders/            # Sample data
├── ml/                     # Machine learning components
│   ├── models/             # Trained ML models
│   ├── processors/         # Data processing scripts
│   └── api/                # ML API endpoints
├── routes/                 # Application routes
├── config/                 # Configuration files
└── README.md               # Project documentation
</pre>

<h2>📋 <strong>PREREQUISITES</strong></h2>
<br>

<p>Before running this application, ensure you have:</p>
<ul>
    <li>PHP 8.0 or higher</li>
    <li>Composer - PHP dependency manager</li>
    <li>Node.js and npm - For frontend asset compilation</li>
    <li>MySQL or PostgreSQL database</li>
    <li>Python 3.8+ with required ML libraries (scikit-learn, pandas, numpy)</li>
    <li>Web server (Apache/Nginx) or Laravel development server</li>
</ul>

<h2>🚀 <strong>INSTALLATION & SETUP</strong></h2>
<br>

<ol>
    <li><strong>Clone the repository</strong>
        <pre><code>git clone https://github.com/your-username/bullyproof-web.git
        cd bullyproof-web</code></pre>
    </li>
    
    <li><strong>Install PHP dependencies</strong>
        <pre><code>composer install</code></pre>
    </li>
    
    <li><strong>Install Node.js dependencies</strong>
        <pre><code>npm install</code></pre>
    </li>
    
    <li><strong>Environment configuration</strong>
        <pre><code>cp .env.example .env php artisan key:generate</code></pre>
    </li>
    
    <li><strong>Database setup</strong>
        <pre><code>php artisan migrate php artisan db:seed</code></pre>
    </li>
    
    <li><strong>Install Python dependencies</strong>
        <pre><code>pip install -r requirements.txt</code></pre>
    </li>
    
    <li><strong>Compile frontend assets</strong>
        <pre><code>npm run dev</code></pre>
    </li>
    
    <li><strong>Start the application</strong>
        <pre><code>php artisan serve</code></pre>
    </li>
</ol>

<h2>🖥️ <strong>USAGE</strong></h2>
<br>

<ol>
    <li><strong>Administrator Login</strong>
        <ul>
            <li>Access the web application through your browser</li>
            <li>Login using your administrative credentials</li>
            <li>Navigate to the dashboard for system overview</li>
        </ul>
    </li>
    
    <li><strong>Managing Incidents</strong>
        <ul>
            <li>Review incident reports with ML-generated cyberbullying percentages</li>
            <li>Investigate cases using provided evidence and analysis</li>
            <li>Update case status and assign follow-up actions</li>
        </ul>
    </li>
    
    <li><strong>Scheduling Appointments</strong>
        <ul>
            <li>Create meetings between involved parties</li>
            <li>Set appropriate time slots and participant notifications</li>
            <li>Monitor appointment status and outcomes</li>
        </ul>
    </li>
    
    <li><strong>User Oversight</strong>
        <ul>
            <li>Monitor user activities and account status</li>
            <li>Take action on accounts showing misuse patterns</li>
            <li>Generate user activity reports</li>
        </ul>
    </li>
</ol>

<h2>🎯 <strong>TARGET USERS</strong></h2>
<br>

<ul>
    <li><strong>University Directors</strong> - High-level oversight and policy decisions</li>
    <li><strong>Administrative Staff</strong> - Daily case management and operations</li>
    <li><strong>IT Administrators</strong> - System maintenance and user management</li>
    <li><strong>Department Heads</strong> - Departmental incident oversight</li>
</ul>

<h2>🔒 <strong>SECURITY & PRIVACY</strong></h2>
<br>

<ul>
    <li>Role-based access control with multi-level authentication</li>
    <li>Encrypted data storage and transmission</li>
    <li>Audit logging for all administrative actions</li>
    <li>GDPR and institutional privacy policy compliance</li>
    <li>Secure handling of sensitive incident reports and personal data</li>
    <li>Regular security updates and vulnerability assessments</li>
</ul>

<h2>🔗 <strong>INTEGRATION</strong></h2>
<br>

<ul>
    <li><strong>Mobile App Sync</strong> - Real-time data synchronization with BullyProof mobile application</li>
    <li><strong>University Systems</strong> - Integration with existing UMAK administrative systems</li>
    <li><strong>Email Services</strong> - SMTP integration for automated notifications</li>
    <li><strong>ML Pipeline</strong> - Seamless integration with Python-based machine learning models</li>
</ul>

<h2>📄 <strong>LICENSE</strong></h2>
<br>

<p>This project is licensed under the MIT License - see the <a href="LICENSE">LICENSE</a> file for details.</p>

<h2>🙏 <strong>ACKNOWLEDGMENTS</strong></h2>
<br>

<ul>
    <li>University of Makati Administration for supporting this administrative initiative</li>
    <li>IT Department for system integration and security guidance</li>
    <li>Administrative staff who provided workflow requirements and feedback</li
</ul>
