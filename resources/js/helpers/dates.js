import moment from 'moment-timezone';

/**
 * Returning string like '1 day ago'
 * @param dateTime Date
 * @returns {string}
 */
export const someXAgo = function (dateTime) {
    if (moment(moment.now()).diff(dateTime, 'minute') < 60) {
        return 'Now';
    }
    if (moment(moment.now()).diff(dateTime, 'hour') < 24) {
        return 'Today';
    }
    if (moment(moment.now()).diff(dateTime, 'day') < 7) {
        return moment(moment.now()).diff(dateTime, 'day') + ' days ago';
    }
    if (moment(moment.now()).diff(dateTime, 'week') < 4) {
        return moment(moment.now()).diff(dateTime, 'week') + ' weeks ago';
    }
    if (moment(moment.now()).diff(dateTime, 'month') < 12) {
        return moment(moment.now()).diff(dateTime, 'month') + ' months ago';
    }
    return moment(moment.now()).diff(dateTime, 'year') + ' years ago';
}
