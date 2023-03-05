import { Link } from "@inertiajs/vue3";
import { ElAvatar } from "element-plus";
import { defineComponent, h } from "vue";

export const ClientProfile = defineComponent({
  props: {
    client: {
      type: Object,
    },
    type: {
      type: String
    },
    name: {
      type: String
    },
    id: {
      type: String
    }
  },
  setup(props) {
        const initials = props.client?.names ? props.client?.names[0] + props.client?.lastnames[0] : props.name.split(' ').map(name => name?.at(0)).join('');
        const type = props.type ?? Object.entries(props.client).reduce((type, [field, value]) => {
          if (field.match(/owner|tenant|lender/) && value == 1) {
            type = field.replace('is_', '');
          }
          return type;
        }, "");

        return {
          initials,
          type
        }


    },
    render() {
      return h('div', { class: 'flex items-center space-x-2 px-4' }, [
        h(ElAvatar, { shape: 'circle', width: 20, height: 20, maxWidth: 20, maxHeight: 20 }, this.initials),
        h('div', { class: 'ml-2 w-full text-left'},  [
          h(Link, {class: 'font-bold text-primary', href: `/contacts/${this.id ?? this.client?.id}/${this.type}`}, this.name ?? this.client?.fullName),
          h('p', { class: 'text-body-1/80 text-sm'}, this.client?.dni)
        ]),
    ]);
    }
});

