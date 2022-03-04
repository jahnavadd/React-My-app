import React, { useEffect, useState } from "react";
import PostService from "../API/PostService";
import PostFilter from "../components/PostFilter";
import PostForm from "../components/PostForm";
import PostList from "../components/PostList";
import MyButton from "../components/UI/button/MyButton";
import Loader from "../components/UI/Loader/Loader";
import MyModal from "../components/UI/MyModal/MyModal";
import Pagintion from "../components/UI/pagination/Pagination";
import { useFetching } from "../hooks/useFetching";
import { usePosts } from "../hooks/usePosts";
import "../styles/App.css";
import "../styles/main.css";
import getPageCount from "../utils/pages";


function Posts() {

  const [posts, setPosts] = useState([])

  const [filter, setFilter] = useState({sort: '', query: ''})

  const [modal, setModal] = useState(false)

  const sortAndSearchPosts = usePosts(posts, filter.sort, filter.query)

  const [totalPages, setTotalPages] = useState(0)
  const [limit, setLimit] = useState(10)
  const [page, setPage] = useState(1)
  
  const [fetchPosts, isPostLoading, postError] = useFetching(async() => {
    const responce = await PostService.getAll(limit, page);
    setPosts(responce.data);

    const totalCount = responce.headers['x-total-count']
    setTotalPages(getPageCount(totalCount, limit))
  })
  
  useEffect(() => {
    fetchPosts()
  }, [page])

  const changePage = (page) => {
    setPage(page)
    fetchPosts()
  }

  const createPost = (newPost) => {
    setPosts([...posts, newPost])
    setModal(false)
  }

  const removePost = (post) => {
    setPosts(posts.filter(p => p.id !== post.id))
  }

  return (
    <div className="Posts">

      <MyButton style={{marginTop: 30}} onClick={() => setModal(true)} >
        Create Post
      </MyButton>
      <MyModal visible={modal} setVisible={setModal}>
        <PostForm create= {createPost} />
      </MyModal>      

      <hr style={{margin: '15px 0'}} />
      
      <PostFilter 
        filter = {filter}
        setFilter = {setFilter}
      />

      {postError &&
        <h1>{postError}</h1>
      }

      {isPostLoading
      ? <Loader />
      : <PostList remove={removePost} title="Posts block" posts={sortAndSearchPosts} />  
      }

      <Pagintion totalPages={totalPages} page={page} changePage={changePage} />
                
    </div>
  );
}

export default Posts;
