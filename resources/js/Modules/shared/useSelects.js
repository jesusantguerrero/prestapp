import { cloneDeep } from "lodash";
import { provide } from "vue";

export function useSelect() {
    const categoryOptions = (categoriesData, groupName = 'subcategories', name = 'categoryOptions', optionParser) => {
        if (!categoriesData || !categoriesData.map) return
        const categories = cloneDeep(categoriesData)
        const options = categories.map(category => {
            if (category) {
                category.type = groupName && category[groupName] ?  'group' : null;
                category.key = category.id;
                category.value = category.id;
                category.label = category.name;
                if (category[groupName]) {
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
