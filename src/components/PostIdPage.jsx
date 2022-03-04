import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import PostService from "../API/PostService";
import { useFetching } from "../hooks/useFetching";
import Loader from "../components/UI/Loader/Loader";

const PostIdPage = () => {
    const param = useParams()
    const [post, setPost] = useState({})
    const [comments, setComments] = useState({})
    const [fetchPostById, isLoading, error] = useFetching(async(id) => {
        const responce = await PostService.getById(id)
        setPost(responce.data)
    })
    const [fetchComments, isCommentsLoading, errorComments] = useFetching(async(id) => {
        const responce = await PostService.getCommentsPost(id)
        setComments(responce.data)
        console.log(responce.data)
    })
    useEffect(() => {
        fetchPostById(param.id)
        fetchComments(param.id)
    }, [])
    return (
        <div>
            <h1>Post {param.id}</h1>
            {isLoading
            ? <Loader />
            : <div><h2>{post.title}</h2><div>{post.body}</div></div>
            }

            {isCommentsLoading
            ? <Loader />
            : <div>
                <h2>Comments</h2>
                {comments.map(comment => 
                    <div><h3>{comment.email}</h3>
                    <div>{comment.body}</div></div>
                )}
               </div>
            }
            
        </div>
         
    )
}

export default PostIdPage