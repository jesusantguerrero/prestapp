import { reactive, watch } from "vue";

interface IFilterConfig {
  name: '',
  value: ''
}

const getFilters = (filters: IFilterConfig[] | string[]): Record<string, any> => {
  return filters.reduce((filters: Record<string, any> , filterConfig: IFilterConfig | string) => {
    const field = typeof filterConfig == 'string' ? filterConfig :filterConfig.name
    filters[field] = null
    return filters;
  }, {})
}

const getFilterValues = (filters: Record<string, any>, prop: string) => {
  return Object.entries(filters).reduce(
    (acc: Record<string, any>, [filterName, filter]) => {
      if (filter) acc[filterName] = filter[prop];
      return acc;
    },
    {}
  )
}
export const useSectionFilters = (filterConfig: IFilterConfig[] | string[], router: any, prop = 'id') => {

  const filters = reactive(getFilters(filterConfig));

  watch(
    () => filters,
    (filters) => {
      const selectedFilters = getFilterValues(filters, prop)

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

  const reset = () => {
    router.get(location.pathname, {}, { preserveState: true });
      
    Object.keys(filters).forEach((key) => {
      filters[key] = ""
    })
  }

  return {
    filters,
    reset
  };
}
