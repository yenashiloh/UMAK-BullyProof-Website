$(document).ready(function() {
    const calendar = $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,basicWeek,basicDay'
        },
        defaultDate: moment().format('YYYY-MM-DD'),
        navLinks: true,
        editable: true,
        eventLimit: true,
        events: appointments,
        eventRender: function(event, element) {
            const statusClass = 'event-' + event.status.toLowerCase().replace(/\s+/g, '-');
            element.addClass(statusClass);

            element.find('.fc-content').html(`
                <span class="fc-time">${moment(event.start).format('h:mma')}</span>
            `);
        },
        eventClick: function(event) {
            let complainantName = event.description.replace('Complainant: ', '');
            
            $('#modalRespondent').text(event.title);
            $('#modalRespondentEmail').text(event.respondent_email); 
            $('#modalDescription').text(complainantName);
            $('#modalComplainantEmail').text(event.complainant_email); 
            $('#modalStatus').text(event.status);
            $('#modalTime').text(moment(event.start).format('MMMM Do YYYY, h:mm a'));
            
            $('#appointmentModal').modal('show');
        }
        
    });

    window.calendar = calendar;
})

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('appointmentForm');
    const submitButton = document.querySelector('button[type="submit"]');
    const loadingOverlay = document.getElementById('loading-overlay');

    function showLoadingOverlay() {
        loadingOverlay.style.display = 'flex';
    }

    function hideLoadingOverlay() {
        loadingOverlay.style.display = 'none';
    }

    hideLoadingOverlay();

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearValidationMessages();

        if (validateForm()) {
            const appointmentDate = document.getElementById('appointmentDate').value;
            const appointmentTime = document.getElementById('appointmentTime').value;
            
            showLoadingOverlay();
            
            submitForm({
                respondent_name: document.getElementById('respondentName').value,
                respondent_email: document.getElementById('respondentEmail').value,
                complainant_name: document.getElementById('complainantName').value,
                complainant_email: document.getElementById('complainantEmail').value,
                appointment_date: appointmentDate,
                appointment_time: appointmentTime
            });
        }
    });

    //form validation 
    function addEventToCalendar(appointmentData) {
        const event = {
            id: appointmentData.appointment_id,
            title: appointmentData.respondent_name,
            start: moment(appointmentData.appointment_datetime).format('YYYY-MM-DD HH:mm:ss'),
            description: 'Complainant: ' + appointmentData.complainant_name,
            status: appointmentData.status,
            respondent_email: appointmentData.respondent_email,   
            complainant_email: appointmentData.complainant_email  
        };
    
        $('#calendar').fullCalendar('renderEvent', event, true);
    }
    

    function validateForm() {
        let isValid = true;

        const respondentName = document.getElementById('respondentName');
        if (!respondentName.value.trim()) {
            showError(respondentName, 'Respondent name is required');
            isValid = false;
        }

        const respondentEmail = document.getElementById('respondentEmail');
        if (!validateEmail(respondentEmail.value)) {
            showError(respondentEmail, 'Please enter a valid email address');
            isValid = false;
        }

        const complainantName = document.getElementById('complainantName');
        if (!complainantName.value.trim()) {
            showError(complainantName, 'Complainant name is required');
            isValid = false;
        }

        const complainantEmail = document.getElementById('complainantEmail');
        if (!validateEmail(complainantEmail.value)) {
            showError(complainantEmail, 'Please enter a valid email address');
            isValid = false;
        }

        const appointmentDate = document.getElementById('appointmentDate');
        if (!appointmentDate.value) {
            showError(appointmentDate, 'Appointment date is required');
            isValid = false;
        }

        const appointmentTime = document.getElementById('appointmentTime');
        if (!appointmentTime.value) {
            showError(appointmentTime, 'Appointment time is required');
            isValid = false;
        }

        return isValid;
    }

    function validateEmail(email) {
        const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return re.test(String(email).toLowerCase());
    }

    function showError(input, message) {
        const feedback = input.nextElementSibling;
        input.classList.add('is-invalid');
        feedback.textContent = message;
    }

    function clearValidationMessages() {
        const inputs = form.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.classList.remove('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback) {
                feedback.textContent = '';
            }
        });
    }

    //submit form
    function submitForm(data) {
        submitButton.disabled = true;
    
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
                    complainant_name: document.getElementById('complainantName').value,
                    appointment_datetime: moment(
                        document.getElementById('appointmentDate').value + ' ' + 
                        document.getElementById('appointmentTime').value
                    ).format('YYYY-MM-DD HH:mm:ss'),
                    status: 'Waiting for Confirmation',
                    respondent_email: document.getElementById('respondentEmail').value,
                    complainant_email: document.getElementById('complainantEmail').value
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
            submitButton.disabled = false;
        });
    }
});