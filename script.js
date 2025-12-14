// Handle form submission
document.getElementById('dailyForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);

    fetch('submit.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success'){
            document.getElementById('successModal').classList.remove('hidden');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => alert('Error submitting form: ' + err));
});

// Modal button redirect to admin panel
document.getElementById('modalButton').addEventListener('click', function(){
    window.location.href = 'admin.html';
});
