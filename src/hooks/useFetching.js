import { useState } from "react";

export const useFetching = (callback) => {
    const [isPostLoading, setIsPostLoading] = useState(true)
    const [error, setError] = useState('')

    const fetching = async (...args) => {
        try {
            setIsPostLoading (true)
            await callback(args)
        } catch (e) {
            setError(e.message)
        } finally {
            setIsPostLoading(false)
        }
    }

    return [fetching, isPostLoading, error]
}