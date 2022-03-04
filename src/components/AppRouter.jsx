import React from "react";
import { Routes, Route } from "react-router-dom";
import About from "../pages/About";
import Posts from "../pages/Posts";
import NotFound from "../pages/NotFound";
import PostIdPage from "./PostIdPage";
import { routes } from "../router";

const AppRouter = () => {
    return (
      <Routes>
        {
          routes.map(route => 
            <Route path={route.path} element={route.component} exact={route.exact} />
          )
        }
        <Route path="posts" element={<Posts />} />
        <Route path="about" element={<About />} />
        <Route path="posts" element={<Posts />} />
        <Route path="posts/:id" element={<PostIdPage />} />
        <Route path="*" element={<NotFound/>}/>
      </Routes>
    )
}

export default AppRouter