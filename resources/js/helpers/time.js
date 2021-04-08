import moment from 'moment-timezone';
import * as numbers from './numbers';

/**
 * Convert seconds to time format
 * @param seconds number - Count of seconds
 * @param withSeconds: boolean
 * @returns {string}
 */
export const convertSecToTime = (seconds, withSeconds = true) => {
    if (!seconds) {
        return `00:00` + (withSeconds ? ':00' : '');
    }
    const h = Math.floor(seconds / 60 / 60);
    const m = Math.floor((seconds - h * 60 * 60) / 60);
    const s = seconds - (m * 60) - (h * 60 * 60);
    let time = `${numbers.addZeroBefore(h,2)}:${numbers.addZeroBefore(m,2)}`;
    if (withSeconds) {
        time += `:${numbers.addZeroBefore(s,2)}`;
    }
    return time;
}

/**
 * Get count of seconds between two dates
 * @param dateStart: dateTime
 * @param dateEnd: dateTime
 * @returns {number}
 */
export const getSecBetweenDates = (dateStart, dateEnd) => {
    if (moment(dateStart) > moment(dateEnd)) {
        dateEnd = moment(dateEnd).add(1, 'day');
    }
    return moment(dateEnd).diff(moment(dateStart), 'seconds');
}

/**
 * Convert seconds to decimal number (hours)
 * @param seconds
 */
export const convertSecToDecHours = seconds => {
    return (seconds / 60 / 60).toFixed(2);
}
