$(document).ready(function() {
    //calendar
    const calendar = $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,basicWeek,basicDay'
        },
        defaultDate: moment().format('YYYY-MM-DD'),
        navLinks: true,
        editable: false,
        eventLimit: true,
        weekends: false,
        businessHours: {
            start: '08:00',
            end: '17:00',
            dow: [1, 2, 3, 4, 5]
        },
        events: appointments,
        dayRender: function(date, cell) {
            // holiday
            if (date.format('MM-DD') === '11-01') {
                cell.append('<span class="holiday-indicator">All Soul\'s Day<br>(Special Non-Working Holiday)</span>');
                cell.addClass('holiday');
            }
            if (date.format('MM-DD') === '11-30') {
                cell.append('<span class="holiday-indicator">Bonifacio Day<br>(Regular Holiday)</span>');
                cell.addClass('holiday');
            }
            if (date.format('MM-DD') === '12-25') {
                cell.append('<span class="holiday-indicator">Christmas<br>(Regular Holiday)</span>');
                cell.addClass('holiday');
            }
            if (date.format('MM-DD') === '12-30') {
                cell.append('<span class="holiday-indicator">Rizal Day<br>(Regular Holiday)</span>');
                cell.addClass('holiday');
            }
            if (date.format('MM-DD') === '01-01') {
                cell.append('<span class="holiday-indicator">New Year\'s Day<br>(Regular Holiday)</span>');
                cell.addClass('holiday');
            }
            if (date.format('MM-DD') === '02-25') {
                cell.append('<span class="holiday-indicator">EDSA People Power Revolution<br>(Special Non-Working Holiday)</span>');
                cell.addClass('holiday');
            }
            if (date.format('MM-DD') === '04-09') {
                cell.append('<span class="holiday-indicator">Araw ng Kagitingan<br>(Regular Holiday)</span>');
                cell.addClass('holiday');
            }
            if (date.format('MM-DD') === '05-01') {
                cell.append('<span class="holiday-indicator">Labor Day<br>(Regular Holiday)</span>');
                cell.addClass('holiday');
            }
            if (date.format('MM-DD') === '06-12') {
                cell.append('<span class="holiday-indicator">Independence Day<br>(Regular Holiday)</span>');
                cell.addClass('holiday');
            }
        },
        eventRender: function(event, element) {
            // ensure status is properly formatted for class names
            const status = event.status ? event.status.toLowerCase().replace(/\s+/g, '-') : 'waiting-for-confirmation';
            element.addClass('event-' + status);
            
            // add background color class based on status
            if (status === 'rescheduled') {
                element.addClass('event-rescheduled-bg');
            }
        
            const startMoment = moment(event.start);
            const endMoment = moment(event.end);
            
            element.find('.fc-content').html(`
                <span class="fc-time text-center">${startMoment.format('h:mm A')} - <br>${endMoment.format('h:mm A')}</span>
            `);
        },
        
        eventClick: function(event) {
            let complainantName = event.description.replace('Complainant: ', '');
            
            $('#modalRespondent').text(event.title);
            $('#modalRespondentEmail').text(event.respondent_email);
            $('#modalDepartmentComplainantEmail').text(event.complainant_department_email);
            $('#modalDescription').text(complainantName);
            $('#modalComplainantEmail').text(event.complainant_email);
            $('#modalDepartmentComplaineeEmail').text(event.complainee_department_email);
            $('#modalStatus').text(event.status);
            $('#modalTime').text(
                `${moment(event.start).format('MMMM Do YYYY, h:mm A')} - ${moment(event.end).format('h:mm A')}`
            );
            
            $('#appointmentModal').modal('show');
        }
    });

    window.calendar = calendar;

    $("#appointmentDate").datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: 0,
        beforeShowDay: function(date) {
            if (date.getMonth() === 10 && date.getDate() === 1) { 
                return [false, "All Soul's Day"];
            }
            if (date.getMonth() === 11 && date.getDate() === 25) { 
                return [false, "Christmas Day(Holiday)"];
            }
            if (date.getMonth() === 0 && date.getDate() === 1) { 
                return [false, "New Year's Day (Holiday)"];
            }
            if (date.getMonth() === 1 && date.getDate() === 25) { 
                return [false, "EDSA People Power Revolution (Holiday)"];
            }
            if (date.getMonth() === 3 && date.getDate() === 9) { 
                return [false, "Araw ng Kagitingan (Regular Holiday)"];
            }
            if (date.getMonth() === 4 && date.getDate() === 1) { 
                return [false, "Labor Day (Regular Holiday)"];
            }
            if (date.getMonth() === 5 && date.getDate() === 12) { 
                return [false, "Independence Day (Regular Holiday)"];
            }
            return [date.getDay() !== 0 && date.getDay() !== 6, ""];
        }
    });

    function generateTimeOptions() {
        const startHour = 8;
        const endHour = 16; 
        const times = [];
        
        for (let hour = startHour; hour <= endHour; hour++) {
            const period = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour > 12 ? hour - 12 : hour;
            
            // options from 8:00 AM to 4:00 PM (no 4:30 PM)
            if (hour < 16) {
                times.push(
                    `${String(displayHour).padStart(2, '0')}:00 ${period}`,
                    `${String(displayHour).padStart(2, '0')}:30 ${period}`
                );
            } else {
                times.push(`${String(displayHour).padStart(2, '0')}:00 ${period}`);
            }
        }
        
        return times;
    }
    
    // initialize the time selectors
    const $startTime = $('#appointmentStartTime');
    const $endTime = $('#appointmentEndTime');
    const timeOptions = generateTimeOptions();
    
    // clear and populate start time dropdown
    function populateStartTime() {
        $startTime.empty();
        $startTime.append('<option value="">Select start time</option>');
        timeOptions.forEach(time => {
            $startTime.append(`<option value="${time}">${time}</option>`);
        });
    }

    // clear and populate end time dropdown
    function populateEndTime(startTime) {
        $endTime.empty();
        $endTime.append('<option value="">Select end time</option>');
        
        if (!startTime) {
            $endTime.prop('disabled', true);
            return;
        }
    }

    function convertTimeToMinutes(timeStr) {
        const [time, period] = timeStr.split(' ');
        let [hours, minutes] = time.split(':').map(Number);
        
        // convert to 24-hour format
        if (period === 'PM' && hours !== 12) {
            hours += 12;
        } else if (period === 'AM' && hours === 12) {
            hours = 0;
        }
        
        return hours * 60 + minutes;
    }

    // initialize start time dropdown
    populateStartTime();

    $startTime.on('change', function() {
        const startTime = $(this).val();
        if (!startTime) {
            $endTime.prop('disabled', true);
            $endTime.html('<option value="">Select end time</option>');
            return;
        }

        const startTimeMinutes = convertTimeToMinutes(startTime);
        
        let endTimeOptions = timeOptions.filter(time => {
            const endTimeMinutes = convertTimeToMinutes(time);
            return endTimeMinutes > startTimeMinutes;
        });

        $endTime.prop('disabled', false);
        $endTime.html('<option value="">Select end time</option>');

        endTimeOptions.forEach(time => {
            $endTime.append(`<option value="${time}">${time}</option>`);
        });
    });

    // initialize end time dropdown as disabled
    $endTime.prop('disabled', true);


    // form validation and submission
    const form = document.getElementById('appointmentForm');
    const submitButton = document.querySelector('button[type="submit"]');
    const loadingOverlay = document.getElementById('loading-overlay');

    function showLoadingOverlay() {
        loadingOverlay.style.display = 'flex';
    }

    function hideLoadingOverlay() {
        loadingOverlay.style.display = 'none';
    }

    function handleServerErrors(errors) {
        for (const field in errors) {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                showError(input, errors[field][0]);
            }
        }
    }

    function validateForm() {
        let isValid = true;
    
        const respondentName = document.getElementById('respondentName');
        if (!respondentName.value.trim()) {
            showError(respondentName, 'Complainee name is required');
            isValid = false;
        }
    
        const respondentEmail = document.getElementById('respondentEmail');
        if (!validateEmail(respondentEmail.value)) {
            showError(respondentEmail, 'Please enter a valid UMAK email address (must end with @umak.edu.ph)');
            isValid = false;
        }
    
        const complaineeDepartmentEmail = document.getElementById('complaineeDepartmentEmail');
        if (!validateEmail(complaineeDepartmentEmail.value)) {
            showError(complaineeDepartmentEmail, 'Please enter a valid UMAK email address (must end with @umak.edu.ph');
            isValid = false;
        }

        const complainantDepartmentEmail = document.getElementById('complainantDepartmentEmail');
        if (!validateEmail(complainantDepartmentEmail.value)) {
            showError(complainantDepartmentEmail, 'Please enter a valid UMAK email address (must end with @umak.edu.ph');
            isValid = false;
        }

        const complainantName = document.getElementById('complainantName');
        if (!complainantName.value.trim()) {
            showError(complainantName, 'Complainant name is required');
            isValid = false;
        }
    
        const complainantEmail = document.getElementById('complainantEmail');
        if (!validateEmail(complainantEmail.value)) {
            showError(complainantEmail, 'Please enter a valid UMAK email address (must end with @umak.edu.ph');
            isValid = false;
        }
    
        const appointmentDate = document.getElementById('appointmentDate');
        if (!appointmentDate.value) {
            showError(appointmentDate, 'Appointment date is required');
            isValid = false;
        }
    
        const startTime = document.getElementById('appointmentStartTime');
        if (!startTime.value) {
            showError(startTime, 'Start time is required');
            isValid = false;
        }
    
        return isValid;
    }
    
    function validateEmail(email) {
        // First, check if it's a valid email format
        const generalEmailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        
        // Then, specifically check for @umak.edu.ph
        const umakEmailRegex = /^[a-zA-Z0-9._%+-]+@umak\.edu\.ph$/;
        
        // Convert to string, trim, and lowercase for consistent checking
        const trimmedEmail = String(email).trim().toLowerCase();
        
        // Return true only if both conditions are met
        return generalEmailRegex.test(trimmedEmail) && umakEmailRegex.test(trimmedEmail);
    }
    
    function showError(input, message) {
        const feedback = input.nextElementSibling;
        input.classList.add('is-invalid');
        feedback.textContent = message;
    }
    
    function clearValidationMessages() {
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.classList.remove('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback) {
                feedback.textContent = '';
            }
        });
    }
    
    // real-time validation
    document.getElementById('respondentName').addEventListener('input', function () {
        clearError(this);
    });
    document.getElementById('respondentEmail').addEventListener('input', function () {
        clearError(this);
    });
    document.getElementById('complaineeDepartmentEmail').addEventListener('input', function () {
        clearError(this);
    });
    document.getElementById('complainantDepartmentEmail').addEventListener('input', function () {
        clearError(this);
    });
    document.getElementById('complainantName').addEventListener('input', function () {
        clearError(this);
    });
    document.getElementById('complainantEmail').addEventListener('input', function () {
        clearError(this);
    });
    document.getElementById('appointmentDate').addEventListener('input', function () {
        clearError(this);
    });
    document.getElementById('appointmentStartTime').addEventListener('input', function () {
        clearError(this);
    });
    
    function clearError(input) {
        const feedback = input.nextElementSibling;
        if (input.value.trim() !== '') {
            input.classList.remove('is-invalid');
            feedback.textContent = '';
        }
    }

    function addEventToCalendar(appointmentData) {
        // combine date and time properly
        const startDateTime = moment(appointmentData.appointment_date + ' ' + 
            convertTo24Hour(appointmentData.appointment_start_time), 'YYYY-MM-DD HH:mm');
        const endDateTime = moment(appointmentData.appointment_date + ' ' + 
            convertTo24Hour(appointmentData.appointment_end_time), 'YYYY-MM-DD HH:mm');

        const event = {
            id: appointmentData.appointment_id,
            title: appointmentData.respondent_name,
            start: startDateTime.format('YYYY-MM-DD[T]HH:mm:ss'),
            end: endDateTime.format('YYYY-MM-DD[T]HH:mm:ss'),    
            description: 'Complainant: ' + appointmentData.complainant_name,
            status: appointmentData.status,
            respondent_email: appointmentData.respondent_email,
            complainant_email: appointmentData.complainant_email,
            complainant_department_email: appointmentData.complainant_department_email,
            complainee_department_email: appointmentData.complainee_department_email
        };
        
        $('#calendar').fullCalendar('renderEvent', event, true);
    }

    // form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearValidationMessages();
    
        if (validateForm()) {
            showLoadingOverlay();
            
            // get the submit button and change its text
            const submitButton = document.querySelector('button[type="submit"]');
            submitButton.textContent = 'Submitting Appointment...';
            submitButton.disabled = true;
            
            const startTime = convertTo24Hour(document.getElementById('appointmentStartTime').value);
            const endTime = convertTo24Hour(document.getElementById('appointmentEndTime').value);
            
            submitForm({
                respondent_name: document.getElementById('respondentName').value,
                respondent_email: document.getElementById('respondentEmail').value,
                complainee_department_email: document.getElementById('complaineeDepartmentEmail').value,

                complainant_name: document.getElementById('complainantName').value,
                complainant_email: document.getElementById('complainantEmail').value,
                complainant_department_email: document.getElementById('complainantDepartmentEmail').value,

                appointment_date: document.getElementById('appointmentDate').value,
                appointment_start_time: startTime,
                appointment_end_time: endTime
            });
        }
    });
    

    function convertTo24Hour(time12h) {
        const [time, modifier] = time12h.split(' ');
        let [hours, minutes] = time.split(':');
        
        if (hours === '12') {
            hours = '00';
        }
        
        if (modifier === 'PM' && hours !== '12') {
            hours = parseInt(hours, 10) + 12;
        }
        
        return `${hours.toString().padStart(2, '0')}:${minutes}`;
    }

    function submitForm(data) {
        const submitButton = document.querySelector('button[type="submit"]');
    
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingOverlay();
            
            if (data.success) {
                const appointmentData = {
                    appointment_id: data.appointment_id,
                    respondent_name: document.getElementById('respondentName').value,
                    respondent_email: document.getElementById('respondentEmail').value,
                    complainee_department_email: document.getElementById('complaineeDepartmentEmail').value,

                    complainant_name: document.getElementById('complainantName').value,
                    complainant_email: document.getElementById('complainantEmail').value,
                    complainant_department_email: document.getElementById('complainantDepartmentEmail').value,
                    
                    appointment_date: document.getElementById('appointmentDate').value,
                    appointment_start_time: document.getElementById('appointmentStartTime').value,
                    appointment_end_time: document.getElementById('appointmentEndTime').value,
                    status: 'Waiting for Confirmation'
                };
    
                addEventToCalendar(appointmentData);
    
                const modal = bootstrap.Modal.getInstance(document.getElementById('newAppointmentModal'));
                modal.hide();
    
                Swal.fire({
                    icon: 'success',
                    title: 'Appointment Created!',
                    text: 'Appointment has been successfully created.',
                    showConfirmButton: true
                });
                
                form.reset();
            } else {
                handleServerErrors(data.errors);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideLoadingOverlay();
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong! Please try again.',
            });
        })
        .finally(() => {
            submitButton.textContent = 'Submit Appointment';
            submitButton.disabled = false;
        });
    }
});

//holiday list
document.addEventListener("DOMContentLoaded", function() {
    const holidays = [
        { date: '06-12', name: 'Independence Day' },
        { date: '08-21', name: 'Ninoy Aquino Day' },
        { date: '08-21', name: 'National Heroes Day' },
        { date: '11-01', name: 'All Souls\' Day' },
        { date: '11-30', name: 'Bonifacio Day' },
        { date: '12-25', name: 'Christmas' },
        { date: '12-30', name: 'Rizal Day' },
        { date: '01-01', name: 'New Year\'s Day' },
        { date: '02-25', name: 'EDSA People Power Revolution' },
        { date: '04-09', name: 'Araw ng Kagitingan' },
        { date: '05-01', name: 'Labor Day' },
        { date: '06-12', name: 'Independence Day' }
    ];

    const today = moment();
    const upcomingHolidays = holidays.filter(holiday => {
        const holidayDate = moment(holiday.date, 'MM-DD');
        return holidayDate.isAfter(today, 'day');
    });

    const holidayListElement = document.getElementById('holiday-list');
    upcomingHolidays.forEach(holiday => {
        const listItem = document.createElement('li');
        listItem.classList.add('holiday-bg');
        listItem.innerHTML = `<strong>${holiday.date} - </strong>${holiday.name}`;
        holidayListElement.appendChild(listItem);
    });
});
