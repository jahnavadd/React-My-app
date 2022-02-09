import React, {useMemo, useRef, useState} from "react";
import Counter from "./components/Counter";
import ClassCounter from "./components/ClassCounter";
import PostList from "./components/PostList";
import PostForm from "./components/PostForm";
import PostFilter from "./components/PostFilter";
import MyModal from "./components/UI/MyModal/MyModal";
import "./styles/App.css"
import MyButton from "./components/UI/button/MyButton";

function App() {

  const [posts, setPosts] = useState([
    {id: 1, title: 'Post 1', body: 'Post 1 Description'},
    {id: 2, title: 'Post 2', body: 'Post 2 Description'},
    {id: 3, title: 'Post 3', body: 'Post 3 Description'},
    {id: 4, title: 'Post 4', body: 'Post 4 Description'},
  ])

  const [filter, setFilter] = useState({sort: '', query: ''})

  const [modal, setModal] = useState(false)

  const sortedPosts = useMemo(() => {
    console.log("!!!")
    if (filter.sort) {
      return [...posts].sort((a, b) => a[filter.sort].localeCompare(b[filter.sort]));
    }
    return posts;
  }, [filter.sort, posts] )

  const sortAndSearchPosts = useMemo(() => {
    return sortedPosts.filter(post => post.title.toLowerCase().includes(filter.query))
  }, [filter.query, sortedPosts])

  const createPost = (newPost) => {
    setPosts([...posts, newPost])
    setModal(false)
  }

  const removePost = (post) => {
    setPosts(posts.filter(p => p.id !== post.id))
  }


  return (
    <div className="App">
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

      <PostList remove={removePost} title="Posts block" posts={sortAndSearchPosts} />
            
    </div>
  );
}

export default App;
