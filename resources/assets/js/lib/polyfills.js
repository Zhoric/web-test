/**
 * Created by nyanjii on 06.05.17.
 */
if (!String.prototype.includes) {
    String.prototype.includes = function(search, start) {
        'use strict';
        if (typeof start !== 'number') {
            start = 0;
        }

        if (start + search.length > this.length) {
            return false;
        } else {
            return this.indexOf(search, start) !== -1;
        }
    };
}

// Polyfill for Date.parse
Date.parse = Date.parse || function(
        a // ISO Date string
    ){
        // turn into array, cutting the first character of the Month
        a = a.split(/\W\D?/);
        // create a new date object
        return new Date(
            // year
            a[3],
            // month (starting with zero)
            // we got only the second and third character, so we find it in a string
            // Jan => an => 0, Feb => eb => 1, ...
            "anebarprayunulugepctovec".search(a[1]) / 2,
            // day
            a[2],
            // hour
            a[4],
            // minute
            a[5],
            // second
            a[6]
        )
    };