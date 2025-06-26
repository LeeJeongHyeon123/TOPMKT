// 로딩 상태 관리를 위한 React Context
import React, { createContext, useContext, useState, ReactNode } from 'react'

interface LoadingState {
  isLoading: boolean
  message?: string
}

interface LoadingContextType {
  loading: LoadingState
  setLoading: (loading: boolean, message?: string) => void
  startLoading: (message?: string) => void
  stopLoading: () => void
}

const LoadingContext = createContext<LoadingContextType | undefined>(undefined)

interface LoadingProviderProps {
  children: ReactNode
}

export const LoadingProvider: React.FC<LoadingProviderProps> = ({ children }) => {
  const [loading, setLoadingState] = useState<LoadingState>({
    isLoading: false,
    message: undefined,
  })

  const setLoading = (isLoading: boolean, message?: string) => {
    setLoadingState({ isLoading, message })
  }

  const startLoading = (message?: string) => {
    setLoadingState({ isLoading: true, message })
  }

  const stopLoading = () => {
    setLoadingState({ isLoading: false, message: undefined })
  }

  const value: LoadingContextType = {
    loading,
    setLoading,
    startLoading,
    stopLoading,
  }

  return (
    <LoadingContext.Provider value={value}>
      {children}
    </LoadingContext.Provider>
  )
}

export const useLoading = (): LoadingContextType => {
  const context = useContext(LoadingContext)
  if (context === undefined) {
    throw new Error('useLoading must be used within a LoadingProvider')
  }
  return context
}

export default LoadingContext