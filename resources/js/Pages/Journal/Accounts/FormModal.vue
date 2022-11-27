<template>
  <account-modal
    v-model:is-open="isVisible"
    :buttons="buttons"
    title="Add an account"
    @update:is-open="$emit('update:is-open', $event)"
    @command="handleCommand"
  >
    <template #body>
      <div class="row">
        <div class="">
          <label for="invoice-description">Category: </label>

          <jet-select
            v-model="account.category_id"
            v-model:selected="category"
            :options="categories"
            placeholder="Select a category"
            class="w-full"
            group-label="name"
            group-items="subcategories"
            label="name"
            key-track="id"
          >
          </jet-select>
        </div>

        <div class="flex mt-5">
          <div class="w-6/12">
            <div class="invoide-form-row form-group">
              <label for="">Name</label>
              <input
                type="text"
                class="form-control"
                v-model="account.name"
                required
                min="1"
              />
            </div>
          </div>

          <div class="w-6/12">
            <div class="invoide-form-row form-group">
              <label for="">Account Id</label>
              <input
                type="text"
                class="form-control"
                v-model="account.display_id"
                required
                min="1"
              />
            </div>
          </div>
        </div>

        <div class="mt-5">
          <label for="">Description</label>
          <textarea class="w-full" v-model="account.description" cols="3" rows="3">
          </textarea>
        </div>
      </div>
    </template>
  </account-modal>
</template>

<script>
import axios from "axios";
import { ElNotification, ElOption, ElOptionGroup, ElSelect } from "element-plus";

export default {
  props: {
    isVisible: Object,
    accountData: [Object, null],
    endpoint: {
      type: String,
      required: true,
    },
    categories: {
      type: Array,
      required: true,
    },
  },
  components: {
    AccountModal,
    ElOption,
    ElOptionGroup,
    ElSelect,
    JetSelect,
  },

  data() {
    return {
      account: {},
      category: null,
      accounts: [],
      isLoading: false,
    };
  },

  watch: {
    accountData: {
      handler(account) {
        if (account) {
          this.account = account;
        }
      },
      deep: true,
      immediate: true,
    },
  },

  computed: {
    buttons() {
      const buttons = [
        {
          name: "cancel",
          text: "Cancel",
          classes: "bg-gray-400 text-white hover:bg-gray-500",
        },
        {
          name: "delete",
          text: "Delete Account",
          classes: "bg-red-400 text-white hover:bg-red-500",
          requireEdit: true,
        },
        {
          name: "save",
          text: "Save",
          classes: "bg-green-400 text-white hover:bg-green-500",
        },
      ];
      return buttons.filter(
        (button) =>
          !button.requireEdit ||
          (button.requireEdit && this.accountData && this.accountData.id)
      );
    },
  },

  methods: {
    addAccount() {
      if (!this.account.name || !this.account.category_id) {
        this.$notify({
          type: "error",
          message: "should specify a category and name",
        });
        return;
      }

      const formData = this.account;

      axios.post("/accounts", formData).then(() => {
        ElNotification({
          title: "Saved",
          message: "Account Saved",
          type: "success",
        });
        this.resetForm(true);
        this.$inertia.reload();
      });
    },

    deleteAccount() {
      this.$http
        .delete(`${this.endpoint}/${this.account.id}`)
        .then((account) => {
          this.$emit("saved");
          this.resetForm(true);
        })
        .catch((err) => {
          this.$notify({
            type: "error",
            message: err.response
              ? err.response.data.status.message
              : "Ha ocurrido un error",
          });
        });
    },

    resetForm(shouldClose) {
      this.account = {};
      if (shouldClose) {
        this.$emit("update:is-open", false);
      }
    },

    searchAccounts(query = "") {
      const url = `/categories?filters[resource_type]=ACCOUNT&filters[depth]=0&filters[name]=%${query}%&sort=index&relationships=subCategories`;
      this.isLoading = true;

      this.$http
        .get(url)
        .then(({ data }) => {
          if (data) {
            this.isLoading = false;
            this.accounts = data;
          }
        })
        .catch(() => {
          this.isLoading = false;
        });
    },

    handleCommand(commandName) {
      switch (commandName) {
        case "save":
          this.addAccount();
          break;
        case "delete":
          this.$emit("update:is-open", false);
          break;
        default:
          this.$emit("update:is-open", false);
          break;
      }
    },
  },
};
</script>
