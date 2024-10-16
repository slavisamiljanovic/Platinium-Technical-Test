import { jwtDecode } from 'jwt-decode'
import { LoggerService } from '@/plugins/services/logger'

// Shape of the token payload.
interface JwtPayload {
  exp: number
  iat: number
  sub: string
  // eslint-disable-next-line
  [key: string]: any // Add other token properties if required.
}

// Logger service.
const loggerService = new LoggerService()

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

const isLoggedIn = (token: string): boolean => {
  let result = false
  loggerService.log('helpers.isLoggedIn() -> token', { logData: token })
  if (token) {
    result = !isTokenExpired(token)
  }
  loggerService.log('helpers.isLoggedIn()', { logData: result })
  return result
}

const isTokenExpired = (token: string): boolean => {
  let result = true
  const decodedToken = decodeToken(token)
  if (decodedToken && decodedToken.exp) {
    const currentTime = Math.floor(Date.now() / 1000)
    loggerService.log('helpers.isTokenExpired() -> decodedToken.exp', { logType: 'warn', logData: new Date(decodedToken.exp * 1000) })
    loggerService.log('helpers.isTokenExpired() -> currentTime', { logType: 'warn', logData: new Date(currentTime * 1000) })
    result = decodedToken.exp < currentTime
  }
  loggerService.log('helpers.isTokenExpired()', { logData: result })
  return result
}

const decodeToken = (token: string): JwtPayload | null => {
  let result = null
  if (token) {
    try {
      result = jwtDecode<JwtPayload>(token)
    } catch (error) {
      loggerService.log('helpers.decodeToken()', { logType: 'error', logData: error })
    }
  }
  return result
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
    isObjectEmpty,
    isLoggedIn,
    isTokenExpired
  }
}
