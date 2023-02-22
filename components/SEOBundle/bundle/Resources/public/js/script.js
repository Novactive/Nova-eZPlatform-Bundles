const uploadBtn = document.getElementsByClassName('novaseo-upload-btn')[0];
const uploadInput = document.getElementsByClassName('novaseo-upload-file')[0];
uploadBtn.addEventListener('click', function() {
    uploadInput.click();
});

uploadInput.addEventListener('change', function(e) {
    var fileName = e.target.files[0].name;
    const uploadPreview = document.getElementsByClassName('novaseo-upload-preview')[0];
    uploadPreview.style.display = 'flex';
    uploadPreview.innerHTML = fileName;
});