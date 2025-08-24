<template>
  <transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0 translate-y-2"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-300"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-2"
  >
    <div
      v-if="visible"
      :class="[
        'fixed top-5 right-5 px-4 py-3 rounded-lg shadow-lg text-white',
        typeClass
      ]"
    >
      {{ message }}
    </div>
  </transition>
</template>

<script setup>
import { ref, watchEffect, computed } from "vue"

const props = defineProps({
  message: String,
  type: {
    type: String,
    default: "success", // success | error | info
  },
  duration: {
    type: Number,
    default: 3000,
  },
})

const visible = ref(false)

const typeClass = computed(() => {
  switch (props.type) {
    case "error":
      return "bg-red-600"
    case "info":
      return "bg-blue-600"
    default:
      return "bg-green-600"
  }
})

watchEffect(() => {
  if (props.message) {
    visible.value = true
    setTimeout(() => {
      visible.value = false
    }, props.duration)
  }
})
</script>
