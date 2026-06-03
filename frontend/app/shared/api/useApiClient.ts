export const useApiClient = () => {
  const { $api } = useNuxtApp()

  return $api
}
