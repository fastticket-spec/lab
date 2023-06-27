import moment from "moment/moment";

export const dateBetween = (startDate, endDate) => {
    if (startDate && endDate) {
        let dateArray = [];
        let currentDate = moment(startDate);
        let stopDate = moment(endDate);
        while (currentDate <= stopDate) {
            dateArray.push(moment(currentDate).format('YYYY-MM-DD'))
            currentDate = moment(currentDate).add(1, 'days');
        }
        return dateArray;
    }
    return [];
}
