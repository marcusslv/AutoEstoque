import { describe, expect, it } from 'vitest'
import { ApiError, getApiErrorMessage, getApiValidationErrors, normalizeApiError } from '../../../app/shared/api/apiErrors'

describe('api errors', () => {
  it('normalizes validation errors with field messages', () => {
    const error = normalizeApiError({
      response: { status: 422 },
      data: {
        message: 'Revise os campos.',
        errors: {
          email: ['E-mail invalido.'],
        },
      },
    })

    expect(error.kind).toBe('validation')
    expect(error.isValidation).toBe(true)
    expect(error.errors?.email).toEqual(['E-mail invalido.'])
  })

  it('normalizes conflict errors', () => {
    const error = normalizeApiError({
      response: { status: 409 },
      data: { message: 'Estoque insuficiente.' },
    })

    expect(error.kind).toBe('conflict')
    expect(error.message).toBe('Estoque insuficiente.')
  })

  it('returns messages and validation errors from ApiError instances', () => {
    const error = new ApiError({
      kind: 'validation',
      statusCode: 422,
      message: 'Campos invalidos.',
      errors: { name: ['Obrigatorio.'] },
    })

    expect(getApiErrorMessage(error)).toBe('Campos invalidos.')
    expect(getApiValidationErrors(error)).toEqual({ name: ['Obrigatorio.'] })
  })
})
