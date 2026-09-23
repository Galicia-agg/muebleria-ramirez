import { definePreset } from '@primeuix/themes';
import Aura from '@primeuix/themes/aura';

const brown = {
    50: '#FAF6F1',
    100: '#F0E4D8',
    200: '#E1C7B0',
    300: '#CBA07E',
    400: '#B47F55',
    500: '#8B5E34',
    600: '#6F4A29',
    700: '#593B21',
    800: '#47301C',
    900: '#3B2818',
    950: '#201409',
};

export const MuebleriaRamirezPreset = definePreset(Aura, {
    semantic: {
        primary: brown,
    },
});
