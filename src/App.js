import React, {useMemo, useRef, useState} from "react";
import Counter from "./components/Counter";
import ClassCounter from "./components/ClassCounter";
import PostList from "./components/PostList";
import PostForm from "./components/PostForm";
import MySelect from "./components/UI/select/MySelect";
import "./styles/App.css"
import MyInput from "./components/UI/input/MyInput";

function App() {

  const [posts, setPosts] = useState([
    {id: 1, title: 'Post 1', body: 'Post 1 Description'},
    {id: 2, title: 'Post 2', body: 'Post 2 Description'},
    {id: 3, title: 'Post 3', body: 'Post 3 Description'},
    {id: 4, title: 'Post 4', body: 'Post 4 Description'},
  ])

  const [searchQuery, setSearchQuery] = useState('')
  
  const [selectedSort, setSelectedSort] = useState('')

  const getSortedPosts = () => {
    if (selectedSort) {
      return [...posts].sort((a, b) => a[selectedSort].localeCompare(b[selectedSort]));
    }
    return posts;
  }
  
  const sortedPosts = getSortedPosts()

  const createPost = (newPost) => {
    setPosts([...posts, newPost])
  }

  const removePost = (post) => {
    setPosts(posts.filter(p => p.id !== post.id))
  }

  const sortPosts = (sort) => {
    setSelectedSort(sort)
  }

  return (
    <div className="App">
      <PostForm create= {createPost} />

      <hr style={{margin: '15px 0'}} />
      <MyInput 
        value = {searchQuery}
        onChange = {e => setSearchQuery(e.target.value)}
        placeholder='Search...'
      />

      <MySelect 
        defaultOption='Sort'
        options={[
          {value: 'title', name: 'By name'},
          {value: 'body', name: 'By description'}
        ]}
        value={selectedSort}
        onChange={sortPosts}
      />

      {
        posts.length !== 0
        ? <PostList remove={removePost} title="Posts block" posts={sortedPosts} />
        : <h1 style={{textAlign: 'center'}}>Empty</h1>
      }
      
    </div>
  );
}

export default App;
