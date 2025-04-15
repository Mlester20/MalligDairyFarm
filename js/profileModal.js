$(document).ready(function () {
    // Trigger the profile modal when the cog icon is clicked
    $(document).on('click', '.nav-link[data-bs-target="#profileModal"]', function () {
        $('#profileModal').modal('show');
    });
});