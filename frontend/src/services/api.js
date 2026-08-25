import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  }
})

export async function baixarArquivo(url, nomeArquivo, params = {}) {
  const resposta = await api.get(url, { responseType: 'blob', params })
  const blobUrl = window.URL.createObjectURL(new Blob([resposta.data]))
  const link = document.createElement('a')
  link.href = blobUrl
  link.setAttribute('download', nomeArquivo)
  document.body.appendChild(link)
  link.click()
  link.remove()
  window.URL.revokeObjectURL(blobUrl)
}

export default api