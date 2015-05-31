function confirmDelete (url) {
    console.log(url);
    $('#deleteBtn').attr('href', url);
    $('#adminDeleteModal').modal('toggle');
}