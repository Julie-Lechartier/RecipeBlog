import './styles/app.css';

import $ from 'jquery';
window.$ = $;
window.jQuery = $;

import 'bootstrap/dist/css/bootstrap.min.css';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import htmx from "htmx.org";
window.htmx = htmx;  // Define htmx globally

import select2 from 'select2';
import "select2/dist/css/select2.min.css";
select2($);

document.addEventListener('DOMContentLoaded', function() {
    if (!window.htmx) {
        console.warn('htmx is not loaded yet!');
        return;
    }
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    dropdownElementList.forEach(function(element) {
        new bootstrap.Dropdown(element);
    });

    $(function() {
        $('.select2-element').select2({
            theme: 'bootstrap-5'
        });
    });
});