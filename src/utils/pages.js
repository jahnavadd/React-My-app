const getPageCount = (totalCount, limit) => {
    return Math.ceil(totalCount / limit)
}

export default getPageCount