<template>
  <div>
    <div class="w-full box px-2 py-2">
      <div
        class="w-full py-2 bg-blue-50 text-base font-bold my-2 px-2 flex mb-3"
      >
        <div class="w-full intro-y pt-3">
          <h1>User Details: {{ id }}</h1>
        </div>
        <div class="float-right">
          <router-link
            v-if="canUpdate"
            :to="{
              name: 'user-edit',
              params: { hisRoute: 'detail' },
            }"
          >
            <button class="btn btn-primary mx-1 intro-x">
              <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                ></path>
              </svg>
            </button>
          </router-link>
        </div>
      </div>
      <div v-if="userLoading">
        <Animation></Animation>
      </div>
      <table class="w-full">
        <tr class="my-2 border-b-2" v-for="(user, index) in user" :key="index">
          <td class="px-2 py-2 text-md font-bold intro-y w-1/3">
            {{ user.label.toUpperCase() }}
          </td>
          <td class="px-4 py-2 text-base intro-y w-2/3">{{ user.value }}</td>
        </tr>
        <tr class="my-2 border-b-2" v-if="signature">
          <td class="px-2 py-2 text-md font-bold intro-y w-1/3 align-baseline">
            SIGNATURE
          </td>
          <td class="px-4 py-2 text-base intro-y w-2/3">
            <img :src="'/' + signature.file_url" style="max-height: 150px">
          </td>
        </tr>
      </table>
    </div>
    <div class="w-full mt-5">
      <div class="flex">
        <div class="w-full py-2 text-base font-bold my-2 px-2 flex">
          <h1>Branches</h1>
        </div>
        <div class="float-right">
          <router-link
            :to="{
              name: 'user-edit',
              params: { hisRoute: 'detail' },
            }"
          >
            <button type="button" class="btn btn-primary w-40 mt-2">
              Attach Branch
            </button>
          </router-link>
        </div>
      </div>
      <div class="w-full box px-2 py-2">
        <div v-if="branchLoading">
          <Animation></Animation>
        </div>
        <table class="w-full">
          <tr class="my-2 border-b-2">
            <th
              class="px-2 py-2 text-md font-bold intro-y"
              v-for="(head, index) in branchHead"
              :key="index"
            >
              {{ head.label.toUpperCase() }}
            </th>
          </tr>
          <tr
            v-for="(bran, index) in branch"
            :key="index"
            class="my-2 border-b-2 text-center"
          >
            <td
              class="px-2 py-2 text-md font-bold intro-y"
              v-for="(inObj, index) in bran"
              :key="index"
            >
              {{ inObj.value }}
            </td>
          </tr>
        </table>
      </div>
    </div>
    <MessageToast />
  </div>
</template>

<script>
import axios from 'axios'
import Animation from '@/components/Animation/Animate'
import MessageToast from '@/components/Toast/MessageToast'
import UserPermissions from '../../../mixins/UserPermissions'

export default {
  mixins: [UserPermissions],

  components: {
    Animation,
    MessageToast,
  },
  data() {
    return {
      id: this.$route.params.id,
      functionCode: 'USER',
      userLoading: true,
      branchLoading: true,
      user: [],
      branch: [],
      branchHead: [],
      signature: null,
    }
  },
  mounted() {
    this.userDetail()
    this.branchDetail()
    this.MassageResult(this.$route.params.result)
  },
  methods: {
    userDetail() {
      axios
        .get('/users/' + this.id)
        .then(response => {
          var data = response.data
          if (data) {
            console.log(this.id)
            let value = [
              {
                label: 'username',
                value: data.username,
              },
              {
                label: 'full name',
                value: data.full_name,
              },
              {
                label: 'email',
                value: data.email,
              },
              {
                label: 'create at',
                value: data.create_at,
              },
              {
                label: 'update at',
                value: data.update_at,
              },
            ]
            this.user = value

            this.signature = response.data.signature
          }
        })
        .finally(() => {
          this.userLoading = false
        })
    },
    branchDetail() {
      axios
        .get('/user-service/branch/' + this.id)
        .then(response => {
          // console.log(response)
          var data = response.data.branch
          if (data) {
            let head = [
              {
                label: 'branch name en',
              },
              {
                label: 'branch code',
              },
              {
                label: 'sequence',
              },
              {
                label: 'create at',
              },
              {
                label: 'update at',
              },
            ]
            this.branchHead = head

            // console.log(data)
            let branchValue = []
            for (var i = 0; i < data.length; i++) {
              var value = [
                {
                  value: data[i].branch_name_en,
                },
                {
                  value: data[i].branch_code,
                },
                {
                  value: data[i].sequence,
                },
                {
                  value: data[i].create_at,
                },
                {
                  value: data[i].update_at ? data[i].update_at : 'N/A',
                },
              ]
              branchValue.push(value)
            }
            this.branch = branchValue
          }
        })
        .finally(() => {
          this.branchLoading = false
        })
    },
    MassageResult(value) {
      if (value) {
        this.$notify(
          {
            group: 'bottom',
            title: 'Success',
            text: value,
          },
          4000
        )
      } else {
        return false
      }
    },
  },
}
</script>

<style>
</style>