import About from "../pages/About";
import Posts from "../pages/Posts";
import NotFound from "../pages/NotFound";
import PostIdPage from "../components/PostIdPage";

export const routes = [
    {path: '/', component: Posts, exact: true},
    {path: 'about', component: About, exact: true},
    {path: 'posts', component: Posts, exact: true},
    {path: 'posts/:id', component: PostIdPage, exact: true},
    {path: '*', component: NotFound, exact: true}
]
