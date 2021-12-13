import './js/jquery.instantSearch'
import $ from 'jquery';
/*
$(function() {
    $('.search-field')
        .instantSearch({
            delay: 100,
        })
        .keyup();
});
*/

$(document).ready(function() {
    $('.search-field').keyup(function(){
        let x = document.forms["myForm"]["q"].value;
        console.log(x);
    })
 });