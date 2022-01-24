import React from "react";
import styled from "styled-components";
import { colors } from "../config/colors";
import logo from "../Assets/images/logo.png";
import { HiMenu } from "react-icons/hi";

const Nav = () => {
  return (
    <Container>
      <Content>
        <Menu />
        <About>About</About>
        <Logo src={logo} />
        <Contact>Contact</Contact>
      </Content>
    </Container>
  );
};

export default Nav;

const Container = styled.div`
  height: 80px;
  width: 100%;
  background-color:  ${colors.primary};
  margin-top: 0;
  display: flex;
  justify-content: center;
  align-items: center;
`;

const Content = styled.div`
  height: 100%;
  width: 75%;
  display: flex;
  justify-content: space-between;
  align-items: center;

  @media screen and (max-width: 760px) {
    width: 80%;
  }
`;

const Menu = styled(HiMenu)`
  display: none;

  @media screen and (max-width: 760px) {
    display: block;
    font-size: 2rem;
    font-weight: 800;
  }
`;

const About = styled.h2`
  color: black;

  @media screen and (max-width: 760px) {
    display: none;
  }
`;
const Logo = styled.img`
  width: 70px;
  height: 70px;
  color:  ${colors.black};
`;

const Contact = styled.h2`
  color: black;

  @media screen and (max-width: 760px) {
    display: none;
  }
`;
