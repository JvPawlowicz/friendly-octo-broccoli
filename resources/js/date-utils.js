/**
 * Utilitários de data usando date-fns
 */

import { format, addDays, differenceInDays, parseISO, isBefore, isAfter, startOfDay, endOfDay } from 'date-fns';
import { ptBR } from 'date-fns/locale';

window.dateUtils = {
    format: (date, pattern = 'dd/MM/yyyy') => {
        try {
            const dateObj = date instanceof Date ? date : parseISO(date);
            return format(dateObj, pattern, { locale: ptBR });
        } catch (e) {
            return '';
        }
    },
    
    formatDateTime: (date) => {
        try {
            const dateObj = date instanceof Date ? date : parseISO(date);
            return format(dateObj, 'dd/MM/yyyy HH:mm', { locale: ptBR });
        } catch (e) {
            return '';
        }
    },
    
    formatTime: (date) => {
        try {
            const dateObj = date instanceof Date ? date : parseISO(date);
            return format(dateObj, 'HH:mm', { locale: ptBR });
        } catch (e) {
            return '';
        }
    },
    
    addDays: (date, days) => {
        try {
            const dateObj = date instanceof Date ? date : parseISO(date);
            return addDays(dateObj, days);
        } catch (e) {
            return new Date();
        }
    },
    
    differenceInDays: (date1, date2) => {
        try {
            const d1 = date1 instanceof Date ? date1 : parseISO(date1);
            const d2 = date2 instanceof Date ? date2 : parseISO(date2);
            return differenceInDays(d1, d2);
        } catch (e) {
            return 0;
        }
    },
    
    isBefore: (date1, date2) => {
        try {
            const d1 = date1 instanceof Date ? date1 : parseISO(date1);
            const d2 = date2 instanceof Date ? date2 : parseISO(date2);
            return isBefore(d1, d2);
        } catch (e) {
            return false;
        }
    },
    
    isAfter: (date1, date2) => {
        try {
            const d1 = date1 instanceof Date ? date1 : parseISO(date1);
            const d2 = date2 instanceof Date ? date2 : parseISO(date2);
            return isAfter(d1, d2);
        } catch (e) {
            return false;
        }
    },
    
    startOfDay: (date) => {
        try {
            const dateObj = date instanceof Date ? date : parseISO(date);
            return startOfDay(dateObj);
        } catch (e) {
            return new Date();
        }
    },
    
    endOfDay: (date) => {
        try {
            const dateObj = date instanceof Date ? date : parseISO(date);
            return endOfDay(dateObj);
        } catch (e) {
            return new Date();
        }
    },
    
    today: () => {
        return new Date();
    },
    
    now: () => {
        return new Date();
    },
};

