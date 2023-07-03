export const PANEL_SIZES = {
    small: 'md:w-3/12',
    large: 'md:w-5/12'
}

export interface IPaginatedData<T> {
  data: T[];
  total: number
}

const demo = import.meta.env.VITE_APP_DEMO;
export const isDemo = Boolean(demo) && demo !== 'false';
