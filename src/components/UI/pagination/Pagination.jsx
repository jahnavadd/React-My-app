import React from "react";
import { usePagination } from "../../../hooks/usePagination";

const Pagintion = ({totalPages, page, changePage}) => {
    
    const pagesArray = usePagination(totalPages)

    return (
      <div className="pagination">
        {pagesArray.map(p => 
          <span 
            onClick={() => changePage(p)}
            key={p} 
            className={page === p ? 'page page_curent' : 'page'}
          >{p}</span>
        )}
      </div>
    )
}

export default Pagintion