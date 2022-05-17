
// Datalist
const List = require('list.js');
const options = {
    valueNames: [ 'ref', 'location', 'info', 'product', 'status' ]
};
const userList = new List('joblist', options);

// Table items listener
const listItems = document.getElementsByClassName('job_item');
Array.from(listItems).forEach((item) => {
    const route = item.dataset.route;
    item.addEventListener('click', () => {
        sessionStorage.setItem('joblistscroll', window.scrollY);
        document.location.href = route;
    })
})

document.addEventListener('DOMContentLoaded', () => {
    window.scrollTo({ top: Number.parseInt(sessionStorage.getItem('joblistscroll')), behavior: 'smooth'});
    sessionStorage.setItem('joblistscroll', '0');
});
