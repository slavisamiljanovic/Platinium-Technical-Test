/**
 * Logger interface.
 */
interface ILoggerOptions {
  logType?: 'debug' | 'info' | 'warn' | 'error',
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  logData?: any,
  show?: boolean
}
interface ILoggerService {
  log (message: string, options?: ILoggerOptions): void
}

/**
 * Logger service.
 */
export class LoggerService implements ILoggerService {
  private environment: string;

  constructor () {
    // Get the environment from process variables (e.g., development, production).
    this.environment = process.env.NODE_ENV || 'development'
  }

  /**
   * Method to log messages with log type and optional context.
   *
   * @param string         message - Message to be displayed in the console.
   * @param ILoggerOptions options - Logger options.
   */
  // eslint-disable-next-line
  public log (message: string, options?: ILoggerOptions): void {
    const logType = options?.logType ? options.logType : 'debug'
    const logData = options?.logData !== undefined ? options.logData : undefined
    const forceLog = options?.show ? options.show : false

    if (this.shouldLog(logType) || forceLog) {
      const logMessage = `[${logType.toUpperCase()}] -> [${message}]`
      // const callLocation = this.getCallLocation()

      if (logData === undefined) {
        if (logType === 'warn') {
          console.warn(logMessage)
        } else if (logType === 'error') {
          console.error(logMessage)
        } else {
          console.info(logMessage) // , callLocation)
        }
      } else {
        if (logType === 'warn') {
          console.warn(logMessage, logData)
        } else if (logType === 'error') {
          console.error(logMessage, logData)
        } else {
          console.info(logMessage, logData) // , callLocation)
        }
      }
    }
  }

  /**
   * Determine whether the log should be shown based on environment or logType.
   *
   * @param   string logType - Log type.
   * @returns Should log?
   */
  private shouldLog (logType: 'debug' | 'info' | 'warn' | 'error'): boolean {
    let shouldLog = true
    if (this.environment === 'production' &&
      (logType === 'info' || logType === 'debug')
    ) {
      // Only log warnings and errors in production.
      shouldLog = false
    } else if (this.environment === 'development' && logType === 'info') {
      // Only log debugs, warnings and errors in development.
      shouldLog = false
    }
    return shouldLog
  }

  /**
   * Use `Error.stack` to identify the file and line number of the logger call.
   *
   * @returns The file and line number of the logger call.
   */
  private getCallLocation (): string {
    let callLocation = 'Unknown'
    const stack = new Error().stack
    if (stack) {
      const stackLines = stack.split('\n')
      // ??? The caller is typically the second line in the stack.
      const callerLine = stackLines[2] || stackLines[1]

      // ??? Get the third line in the stack, which corresponds to the caller.
      // const callerLine = stackLines[3]

      // Extracting the filename and line number from the stack trace.
      const match = callerLine.match(/(?:(?:webpack-internal:\/\/\/)|(?:.*?\/))?(src\/[^:]*?)(?::(\d+)):/)
      if (match && match[1] && match[2]) {
        callLocation = `${match[1]}:${match[2]}`
      }
    }
    return `[${callLocation}]`
  }
}
