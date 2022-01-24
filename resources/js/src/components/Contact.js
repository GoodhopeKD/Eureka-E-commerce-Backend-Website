import React from "react";
import styled from "styled-components";
import { colors } from "../config/colors";

const Contact = () => {
  return (
    <Container>
      <Left>
        <Header>Contact</Header>
        <Form>
          <Name placeholder="Name(s)" />
          <Email type="email" placeholder="@email" />
          <Message type="text" placeholder="Enter a message....." />
          <Button type="submit" >Submit</Button>
        </Form>
      </Left>
      <Right></Right>
    </Container>
  );
};

export default Contact;

const Container = styled.div`
  height: 80vh;
  width: 100%;
  margin-top: 0;
  background-color: ${colors.gray};
  display: flex;
  justify-content: center;
  align-items: center;
  
  @media screen and (max-width: 760px) {
    display: block;
    
  }
`;

const Right = styled.div`
  height: 100%;
  width: 50%;
  color: white;
`;

const Left = styled.div`
  height: 100%;
  width: 40%;
  padding: 5rem 8rem;
`;

const Header = styled.h2`
  font-size: 3.5rem;
  font-weight: bold;
  color: ${colors.primary};
`;

const Form = styled.form`

`;

const Name = styled.input`
  width: 80%;
  height: 3rem;
  margin-bottom: 1.5rem;
  border: 1px solid ${colors.primary};
  border-radius: 0.8rem;
  padding-left: 1rem;
  font-size: 1.1rem;
`;

const Email = styled.input`
  width: 80%;
  height: 3rem;
  margin-bottom: 1.5rem;
  border: 1px solid ${colors.primary};
  border-radius: 0.8rem;
  padding-left: 1rem;
  font-size: 1.1rem;
`;

const Message = styled.textarea`
  width: 80%;
  height: 7rem;
  border: 1px solid ${colors.primary};
  border-radius: 0.8rem;
  padding: 1rem;
  font-size: 1.1rem;
  margin-bottom: 1.5rem;
`;

const Button = styled.button`
  width: 8rem;
  height: 3rem;
  border-radius: 3rem;
  border: 1px solid ${colors.primary};
`;
