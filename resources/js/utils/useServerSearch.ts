import { router } from '@inertiajs/vue3';
import { Inertia } from "@inertiajs/inertia";
import { format, parseISO } from "date-fns";
import { reactive, Ref, watch }  from "vue"
import { composeRangeYears } from ".";
import { debounce } from 'lodash';

interface IServerSearchData {
    filters: Record<string, string>
    dates: IDateSpan
    limit?: number
    relationships: string
    search?: string
    sorts?: string
    page?: number
}

interface IServerSearchOptions {
    manual?: Boolean,
    mainDateField?: string,
}

interface IDateSpan {
    startDate: Date,
    endDate?: Date
}

interface ISearchState {
  filters: Record<string, string>,
  dates: IDateSpan,
  sorts: string,
  limit: number,
  relationships: string,
  search?: string
  page?: number
}

const DEFAULT_CONFIG = {
  search: "",
  filters: {
    releaseYear: {
      label: "Year",
      options: composeRangeYears(1996),
    },
    programType: {
      label: "Type",
      options: ["movie", "series"],
    },
  },
  sorts: {
    title: {
      label: "Name",
      value: "",
      direction: "",
    },
    releaseYear: {
      label: "Year",
      value: "",
      direction: "",
    },
  },
};

export const filterParams = (mainDateField: string, externalFilters: Record<string, string>, dates: IDateSpan) => {
    let filters = [];
    if (externalFilters) {
        Object.entries(externalFilters).forEach(
            ([name, value]) => {
                if (value) {
                    filters.push(`filter[${name}]=${value}`);
                }
            }
        );
    }

    if (dates.startDate) {
     try {
       let dateFilterValue = format(dates.startDate, 'yyyy-MM-dd');
       if (dates.endDate) {
         dateFilterValue += `~${format(dates.endDate, 'yyyy-MM-dd')}`;
       }
       filters.push(`filter[${mainDateField}]=${dateFilterValue}`);
     } catch (e) {
      console.log('bad dates');
      return filters.join('&');
     }
    }

    return filters.join("&");
}

export const getGroupParams = (groupValue:string) => {
    return `group=${groupValue}`;
}

export const getRelationshipsParams = (relationships: string) => {
    return relationships && `relationships=${relationships}`
}

export const parseParams = (state: ISearchState) => {
  let params = [
      filterParams('date', state.filters, state.dates),
      getRelationshipsParams(state.relationships)
  ];

  if (state.search) {
    params.unshift(`search=${state.search}`);
  }


  return params.filter(value => value?.trim()).join("&");
}

export const updateSearch = (newUrl: string) => {
  router.get(newUrl, undefined, {
    preserveState: true,
  })
}

export const useServerSearch = (serverSearchData: Ref<IServerSearchData>, onUrlChange: Function, options: IServerSearchOptions = {}) => {
    const dates = parseDateFilters(serverSearchData)
    const state = reactive<ISearchState>({
        filters: {
            ...serverSearchData.value.filters,
            date: null
        },
        dates: {
            startDate: dates.startDate,
            endDate: dates.endDate,
        },
        sorts: serverSearchData.value.sorts,
        limit: serverSearchData.value.limit,
        relationships: serverSearchData.value.relationships,
        search: serverSearchData.value.search,
        page: serverSearchData.value.page
    });

   
    watch(
        () => state,
        debounce((paramsConfig) => {
          const urlParams = parseParams(paramsConfig);
          const currentUrl = window.location.toString()

          if (urlParams != currentUrl && !options.manual) {
              onUrlChange && onUrlChange(urlParams);
            }
        }, 400),
        { deep: true }
    );

    const executeSearch = () => {
      const url = parseParams(state)
      onUrlChange && onUrlChange(url);
    }

    function parseDateFilters(options: Ref<IServerSearchData>) {
        const dates = options?.value.filters?.date ? options.value.filters.date.split('~') : [null, null]

        return {
            startDate: parseISO(dates[0]),
            endDate: dates.length == 2 ? parseISO(dates[1]) : null
        }
    }

    const reset = () => {
      state.search = "";
      state.filters = {};
      state.sorts = "";

      executeSearch()
    };

    return {
        state,
        executeSearch,
        updateSearch,
        reset,
    }
}