import { ref } from "vue"

export const toastMessage = ref<string | null>(null)
export const toastType = ref<string>("success")

export function notify(message: string, type: "success" | "error" | "info" = "success") {
  toastMessage.value = message
  toastType.value = type
}
