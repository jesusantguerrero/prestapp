import { cloneDeep } from "lodash";
import { provide } from "vue";

interface IAccountCategory {
  id: number;
  type: string | null;
  key: string | number;
  value: string|number;
  name: string;
  label: string;
  subcategories: IAccountCategory[]
  children: IAccountCategory[]
}
export function useSelect() {
    const categoryOptions = (categoriesData: IAccountCategory[], groupName = 'subcategories', name = 'categoryOptions', optionParser) => {
        if (!categoriesData || !categoriesData.map) return
        const categories = cloneDeep(categoriesData)
        const options = categories.map(category => {
            if (category) {
              // @ts-ignore
                category.type = groupName && category[groupName] ?  'group' : null;
                category.key = category.id;
                category.value = category.id;
                category.label = category.name;
                // @ts-ignore
                if (category[groupName]) {
                  // @ts-ignore
                    category.children = categoryOptions(category[groupName], false, false, optionParser);
                }
            }
            return optionParser ? optionParser(category) : category;
        })
        if (name) {
            provide(name, options);
        }
        return options;
    }

    return {
        categoryOptions
    }
}
