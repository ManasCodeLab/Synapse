  // Enhanced client-side validation
  document.querySelector('form[enctype="multipart/form-data"]').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('research_paper');
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const validTypes = ['application/pdf', 'application/msword', 
                           'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        
        if (!validTypes.includes(file.type)) {
            alert('Please upload only PDF, DOC, or DOCX files');
            e.preventDefault();
            return;
        }
        
        if (file.size > maxSize) {
            alert('File size exceeds 5MB limit');
            e.preventDefault();
        }
    }
});