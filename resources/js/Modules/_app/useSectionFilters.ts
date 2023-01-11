import { reactive, watch } from "vue";

interface IFilterConfig {
  name: '',
  value: ''
}

const getFilters = (filters: IFilterConfig[] | string[]) => {
  return filters.reduce((filters, filterConfig) => {
    filters[filterConfig?.name ?? filterConfig] = null
    return filters;
  }, {})
}

const getFilterValues = (filters: Record<string, any>, prop: string) => {
  return Object.entries(filters).reduce(
    (acc, [filterName, filter]) => {
      if (filter) acc[filterName] = filter[prop];
      return acc;
    },
    {}
  )
}
export const useSectionFilters = (filterConfig: IFilterConfig[], router: any, prop = 'id') => {

  const filters = reactive(getFilters(filterConfig));

  watch(
    () => filters,
    (filters) => {
      const selectedFilters = getFilterValues(filters, prop)
      console.log(selectedFilters, filters)

      router.get(
        location.pathname,
        {
          filters: selectedFilters,
        },
        { preserveState: true }
      );
    },
    { deep: true }
  );

  return filters;
}
