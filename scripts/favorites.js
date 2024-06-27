// JavaScript function to handle form submission for toggling favorites
function submitForm(type, id, action, favorite) {
    // Set values of hidden form elements
    document.getElementById('type').value = type;
    document.getElementById('id').value = id;
    document.getElementById('action').value = action;

    // Set the data-favorite attribute in the form
    document.getElementById('favorites').value = favorite;

    // Change the icon only if it's not already marked as favorite
    const button = document.querySelector(`button[data-id="${id}"][data-type="${type}"]`);
    if (button) {
        const isFavorite = button.getAttribute('data-favorite') === 'true';
        if (!isFavorite) {
            button.querySelector('i').className = 'bi bi-heart-fill'; // Example of icon change
            button.setAttribute('data-favorite', 'true');
        }
    }

    // Submit the form
    document.getElementById('toggle-favorite-form').submit();
}
