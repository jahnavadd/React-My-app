import React from "react";
import cl from "./MyModal.module.css" 
import { CSSTransition } from 'react-transition-group';

const MyModal = ({children, visible, setVisible}) => {
    const rootClass = [cl.myModal]
    if (visible) rootClass.push(cl.active)

    return (
        <div className={rootClass.join(' ')} onClick={() => setVisible(false)}>
          
        <CSSTransition
                in={visible}
                timeout={3000}
                classNames="alert"
          >
            <div className={cl.myModalContent} onClick={(e) => e.stopPropagation()}>
                {children}
            </div>
        </CSSTransition>
        </div>
        
    )
}

export default MyModal