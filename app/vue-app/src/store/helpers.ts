let loadingCount = 0
// eslint-disable-next-line
const subscribers: any[] = []

const loaderNotifySubscribers = () => {
  const isLoading = loadingCount > 0
  subscribers.forEach((callback) => callback(isLoading))
}

// eslint-disable-next-line
const loaderSubscribe = (callback: any) => {
  subscribers.push(callback)
}

// eslint-disable-next-line
const loaderUnsubscribe = (callback: any) => {
  const index = subscribers.indexOf(callback)
  if (index > -1) {
    subscribers.splice(index, 1)
  }
}

const loaderStartLoading = () => {
  loadingCount++
  loaderNotifySubscribers()
}

const loaderStopLoading = () => {
  loadingCount = Math.max(0, loadingCount - 1)
  loaderNotifySubscribers()
}

// eslint-disable-next-line
const handleApiError = (error: any, defaultErrorMessage?: string): string => {
  let errorMessage = defaultErrorMessage || 'An eror has occured. Please try again later.'

  if (error.response &&
    error.response.data &&
    typeof error.response.data['hydra:description'] !== 'undefined'
  ) {
    errorMessage = error.response.data['hydra:description']
  }

  return errorMessage
}

const helperIsOdd = (index: number): boolean => {
  return index % 2 === 0
}

/*
const helperFormatDateTime = (date: Date): string => {
  const padZero = (num: number): string => num.toString().padStart(2, '0')

  const day = padZero(date.getDate())
  const month = padZero(date.getMonth() + 1) // Months are 0-based in JS.
  const year = date.getFullYear()

  const hours = padZero(date.getHours())
  const minutes = padZero(date.getMinutes())
  const seconds = padZero(date.getSeconds())

  return `${day}.${month}.${year} ${hours}:${minutes}:${seconds}`
}
*/

/**
 * Formats a date into 'dd.mm.yyyy hh:mm:ss' using the built-in toLocaleString() method.
 * @param   date The Date object to format.
 * @returns      Formatted date string in 'dd.mm.yyyy hh:mm:ss' format.
 */
const helperFormatDateTime = (date: Date): string => {
  return date.toLocaleString('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false // 24-hour format.
  }).replace(/\//g, '.').replace(',', '') // Replace / with . and remove the comma.
}

// eslint-disable-next-line
const isObjectEmpty = (obj: any): boolean => {
  return Object.keys(obj).length === 0
}

export function useHelper () {
  return {
    loaderStartLoading,
    loaderStopLoading,
    loaderSubscribe,
    loaderUnsubscribe,
    handleApiError,
    helperIsOdd,
    helperFormatDateTime,
    isObjectEmpty
  }
}
